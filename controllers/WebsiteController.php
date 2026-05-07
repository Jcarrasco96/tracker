<?php

namespace app\controllers;

use app\core\Controller;
use app\core\services\Response;
use app\core\widgets\Alert;
use app\models\Website;
use Random\RandomException;

class WebsiteController extends Controller
{

    /**
     * @throws RandomException
     */
    public function actionCreate(): string|Response
    {
        $isAjaxPost = $this->request->isAjax() && $this->request->isPost();

        if ($isAjaxPost) {
            $errors = [];

            $domain = $this->request->post('domain');

            if (!$domain) {
                $errors['domain'] = "Domain is required.";
            }

            if ($errors) {
                return $this->asJson([
                    'success' => false,
                    'errors' => $errors
                ]);
            }

            if (isset($_POST['_validate'])) {
                return $this->asJson(['success' => true]);
            }

            $website = new Website([
                'domain' => strtoupper($domain),
            ]);
            $website->create();

            Alert::flash('success', 'Website created successfully.');

            return $this->asJson(['success' => true]);
        }

        return $this->renderPartial('create');
    }

    public function actionDelete(string $website): Response
    {
        $deleted = Website::delete($website);

        if ($deleted) {
            Alert::flash('success', 'Website deleted successfully.');
            return $this->asJson(['success' => true]);
//            return $this->asJson(['status' => 200, 'message' => 'Website eliminado correctamente.']);
        }

//        Alert::flash('success', 'Website created successfully.');
//        return $this->asJson(['status' => 400, 'message' => 'No se pudo eliminar el producto.']);
        return $this->asJson(['success' => false]);
    }

}