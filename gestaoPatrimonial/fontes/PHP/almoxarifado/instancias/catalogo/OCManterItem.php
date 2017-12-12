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
    * Página Oculta de Funções
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.06

    $Id: OCManterItem.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

$stPrograma = "ManterItem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

function montaAtributos(&$obRegra, $inCodClassificacao)
{
    $stHtml = '';
    if ($inCodClassificacao) {
      $inCodClassificacao = str_replace('.', '', $inCodClassificacao);
        if ($_REQUEST['inCodigo']) {
           $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( array("cod_catalogo"=>$_REQUEST['inCodCatalogo'], "cod_classificacao"=>$inCodClassificacao, "cod_item" => $_REQUEST['inCodigo'] ));
           $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
        } else {
           $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->setChavePersistenteValores( array("cod_catalogo"=>$_REQUEST['inCodCatalogo'], "cod_classificacao"=>$inCodClassificacao ));
           $obRegra->obRAlmoxarifadoClassificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
        }

        // atributos sendo setados no objeto para depois serem inseridos no formulario.
        $obFormulario = new Formulario;
        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setLabel($_REQUEST['boTemMovimentacao']);
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributos_" );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );
        $obMontaAtributos->recuperaValores();
        $obMontaAtributos->geraFormulario ( $obFormulario );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHtml();
    }
    $js = ' d.getElementById(\'spnListaAtributos\').innerHTML = \''. $stHtml .'\';';

    return $js;
}

$obRegra = new RAlmoxarifadoCatalogoItem;
$obIMontaClassificacao = new IMontaClassificacao();
// Acoes por pagina
switch ($stCtrl) {
    case "MontaNiveisCombo":
       if ($_REQUEST['inCodCatalogo']) {
          $obFormulario   = new Formulario;
          $obIMontaClassificacao->setCodigoCatalogo( $_REQUEST['inCodCatalogo'] );
          if ( strpos($_SERVER['HTTP_REFERER'], $pgFilt)!==false ) {
             $obIMontaClassificacao->setUltimoNivelRequerido( false );
             $obIMontaClassificacao->setClassificacaoRequerida( false );
          } else {
             $obIMontaClassificacao->setUltimoNivelRequerido( true );
             $obIMontaClassificacao->setClassificacaoRequerida( true );
          }
          $obIMontaClassificacao->geraFormulario( $obFormulario );
          $obFormulario->montaInnerHTML();
          $js = ' d.getElementById(\'spnListaClassificacao\').innerHTML = \''.$obFormulario->getHtml() .'\';';

          $obFormulario->obJavaScript->montaJavaScript();
          $stValida = $obFormulario->obJavaScript->getInnerJavaScript();
          $js .= " f.stValida.value = '".$stValida."';";

          if ( Sessao::read('transf3') ) {
             $obIMontaClassificacao->setCodigoCatalogo       ( $_REQUEST["inCodCatalogo"]   );
             $obIMontaClassificacao->setCodEstruturalReduzido( Sessao::read('transf3') );
             $js .= $obIMontaClassificacao->preencheCombos($_REQUEST['inNumNiveis']);
             $obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo       ( $_REQUEST["inCodCatalogo"]   );
             $obRegra->obRAlmoxarifadoClassificacao->setEstrutural( Sessao::read('transf3'));
             $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao( $rsCodClassificacao);

             if ( $rsCodClassificacao->getCampo('cod_classificacao')) {
                $js .= montaAtributos($obRegra, $rsCodClassificacao->getCampo('cod_classificacao'));
             }
          }
       } else {
          $js = ' d.getElementById(\'spnListaClassificacao\').innerHTML = \'\';';
       }
       SistemaLegado::executaFrameOculto($js);
    break;
    case "preencheAtributos":
       $stNomeUltimoCombo = "inCodClassificacao_".($_REQUEST['inNumNiveisClassificacao']-1);
       $stUltimoCombo = $_REQUEST[$stNomeUltimoCombo];
       $arUltimoCombo = explode('-', $stUltimoCombo );
       $inCodClassificacao = $arUltimoCombo[2];
       if (!$inCodClassificacao) {
             $obRegra->obRAlmoxarifadoClassificacao->obRAlmoxarifadoCatalogo->setCodigo       ( $_REQUEST["inCodCatalogo"] );
             $obRegra->obRAlmoxarifadoClassificacao->setEstrutural( $_REQUEST['stChaveClassificacao']);
             $obRegra->obRAlmoxarifadoClassificacao->recuperaCodigoClassificacao( $rsCodClassificacao);

             $inCodClassificacao = $rsCodClassificacao->getCampo('cod_classificacao');
       }
       $js .=  montaAtributos($obRegra, $inCodClassificacao);
       sistemaLegado::executaFrameOculto($js);
    break;
}
?>
