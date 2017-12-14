<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página de Formulário Almoxarifado
    * Data de Criação   : 28/10/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Id: FMManterAlmoxarifado.php 61639 2015-02-19 13:05:36Z diogo.zarpelon $

    * Casos de uso: uc-03.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarifado.class.php");

$stPrograma = "ManterAlmoxarifado";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$ob = new RAlmoxarifadoAlmoxarifado;

$stAcao = $request->get('stAcao');

$inCodigo = $_REQUEST['inCodigo'];

$inCGMAlmoxarifado = $_REQUEST['inCGMAlmoxarifado'];
$stNomCGMAlmoxarifado = $_REQUEST['stNomCGMAlmoxarifado'];

$inCGMResponsavel = $_REQUEST['inCGMResponsavel'];
$stNomCGMResponsavel = $_REQUEST['stNomCGMResponsavel'];

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCod = new Hidden;
$obHdnCod->setName("inCodigo");
$obHdnCod->setId("inCodigo");
$obHdnCod->setValue($inCodigo);

$obLblCodigo= new Label;
$obLblCodigo->setRotulo ( "Código"  );
$obLblCodigo->setValue  ( $inCodigo );

$obBscCGMAlmoxarifado = new IPopUpCGM($obForm);
$obBscCGMAlmoxarifado->setId                    ( 'stNomCGMAlmoxarifado' );
$obBscCGMAlmoxarifado->setRotulo                ( 'Almoxarifado'  );
$obBscCGMAlmoxarifado->setTitle                 ( 'Informe o CGM relacionado ao almoxarifado');
$obBscCGMAlmoxarifado->setTipo                  ( 'juridica'                );
$obBscCGMAlmoxarifado->setValue                 ( $stNomCGMAlmoxarifado  );
$obBscCGMAlmoxarifado->obCampoCod->setSize      (10);
$obBscCGMAlmoxarifado->obCampoCod->setName      ( 'inCGMAlmoxarifado'    );
$obBscCGMAlmoxarifado->obCampoCod->setId        ( 'inCGMAlmoxarifado'    );
$obBscCGMAlmoxarifado->obCampoCod->setValue     ( $inCGMAlmoxarifado     );
$obBscCGMAlmoxarifado->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaDadosAlmoxarifado', 'inCGMAlmoxarifado');");

$obBscCGMResponsavel = new IPopUpCGM($obForm);
$obBscCGMResponsavel->setId                    ('stNomCGMResponsavel');
$obBscCGMResponsavel->setRotulo                ( 'Responsável'       );
$obBscCGMResponsavel->setTipo                  ('fisica'           );
$obBscCGMResponsavel->setTitle                ( 'Informe o CGM relacionado ao responsável pelo almoxarifado');
$obBscCGMResponsavel->setValue                 ( $stNomCGMResponsavel);
$obBscCGMResponsavel->obCampoCod->setName      ( 'inCGMResponsavel' );
$obBscCGMResponsavel->obCampoCod->setSize      (10);
$obBscCGMResponsavel->obCampoCod->setValue     ( $inCGMResponsavel   );

if ($stAcao == "alterar") {
    $rsLocalizacao = new recordset();
    $ob->setCodigo($inCodigo);
    $obErro = $ob->consultar();

    if (!($obErro->ocorreu())) {
        $ob->obRCGMAlmoxarifado->consultar($rsCGM);
        $arCGM = $rsCGM->arElementos[0];

        $stEndereco = $arCGM['tipo_logradouro'].' '.$arCGM['logradouro'];

        if (trim($arCGM['numero'])) {
            $stEndereco .= ', '.$arCGM['numero'];
        }

        if (trim($arCGM['complemento'])) {
            $stEndereco .= ', '.$arCGM['complemento'];
        }

        if (trim($arCGM['bairro'])) {
            $stEndereco .= ', '.$arCGM['bairro'];
        }

        $stTelefone = '';

        if (trim($arCGM['fone_residencial']) != '') {
            $stTelefone = $arCGM['fone_residencial'];

            if (trim($arCGM['ramal_residencial']) != '') {
                $stTelefone .= 'Ramal: '.$arCGM['ramal_residencial'];
            }
        }

        if (trim($arCGM['fone_comercial']) != '') {
            if ($stTelefone != '') {
                $stTelefone .= ', ';
            }

            $stTelefone .= $arCGM['fone_comercial'];

            if (trim($arCGM['ramal_comercial']) != '') {
                $stTelefone .= 'Ramal: '.$arCGM['ramal_comercial'];
            }
        }

        if (trim($arCGM['fone_celular']) != '') {
            if ($stTelefone != '') {
                $stTelefone .= ', ';
            }

            $stTelefone .= $arCGM['fone_celular'];
        }

    }
}

$obLblEndereco = new Label;
$obLblEndereco->setRotulo('Endereço');
$obLblEndereco->setId    ('stEndereco');
$obLblEndereco->setValue ($stEndereco);

$obLblTelefone= new Label;
$obLblTelefone->setRotulo('Telefone');
$obLblTelefone->setId    ('stTelefone');
$obLblTelefone->setValue ($stTelefone);

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->setAjuda             ("UC-03.03.01");
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);

$obFormulario->addTitulo            ( ucwords($stAcao)." Almoxarifado" );

if ($stAcao != "incluir") {
    $obFormulario->addHidden        ( $obHdnCod    );

    if ($stAcao == 'alterar') {
        $obFormulario->addComponente( $obLblCodigo );
    }
}

$obFormulario->addComponente        ( $obBscCGMAlmoxarifado    );
$obFormulario->addComponente        ( $obLblEndereco           );
$obFormulario->addComponente        ( $obLblTelefone           );
$obFormulario->addComponente        ( $obBscCGMResponsavel     );

if ($stAcao=="incluir") {
    $obFormulario->OK      ();
} else {
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
