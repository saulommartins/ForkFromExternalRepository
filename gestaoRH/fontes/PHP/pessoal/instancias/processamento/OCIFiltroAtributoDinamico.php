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
    * Oculto do componente IFiltroAtributoDinamico
    * Data de Criação: 10/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2007-11-12 14:44:35 -0200 (Seg, 12 Nov 2007) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpanAtributo(Request $request)
{
    $rsAtributos = new RecordSet();

    if (trim($request->get('inCodAtributo')) != '') {
        include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
        $obRCadastroDinamico = new RCadastroDinamico();
        $obRCadastroDinamico->setCodCadastro($request->get('cod_cadastro'));
        $obRCadastroDinamico->obRModulo->setCodModulo($request->get('cod_modulo'));
        $obRCadastroDinamico->setChavePersistenteValores( array('cod_atributo'=>$request->get('inCodAtributo')) );
        $obRCadastroDinamico->recuperaAtributosSelecionados($rsAtributos);
    }

    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo     ( "Atributos"  );
    $obMontaAtributos->setName       ( "Atributo_"  );
    $obMontaAtributos->setRecordSet  ( $rsAtributos );

    $obHdnCodCadastro = new hidden();
    $obHdnCodCadastro->setName("inCodCadastro");
    $obHdnCodCadastro->setValue($rsAtributos->getCampo("cod_cadastro"));

    $obHdnDescCadastro = new hidden();
    $obHdnDescCadastro->setName("stDescCadastro");
    $obHdnDescCadastro->setValue($rsAtributos->getCampo("nom_atributo"));

    $obFormulario = new Formulario();
    $obFormulario->addHidden($obHdnCodCadastro);
    $obFormulario->addHidden($obHdnDescCadastro);
    $obMontaAtributos->geraFormulario( $obFormulario );

    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    //linha comentada para verificar o erro que está vindo de alguma configuracao que foi setada errada pelo usuario
    //Provavel parametros do usuario estão errados, mas nao conseguimos reproduzir o erro em desenvolvimento
    //$stEval = str_replace("\"", "'", $stEval);
    $stHtml = $obFormulario->getHTML();
    $stJs .= "jq('#spnAtributo').html('".$stHtml."');  \n";
    $stJs .= "jq('#hdnTipoFiltro').val('".$stEval."'); \n";

    return $stJs;
}

switch ( $request->get('stCtrl') ) {
    case "gerarSpanAtributo":
        $stJs = gerarSpanAtributo($request);
        break;
}
if ($stJs) {
    echo $stJs;
}
?>
