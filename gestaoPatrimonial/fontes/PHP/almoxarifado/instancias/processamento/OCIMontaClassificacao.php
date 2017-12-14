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
    * Oculto de Relatório de Concessão de Vale-Tranporte
    * Data de Criação: 07/11/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * Casos de uso: uc-03.03.05
                    uc-03.04.03
*/

/*
$Log$
Revision 1.4  2006/09/11 17:29:14  fernando
Alterado nome das variavéis internas para poder funcionar com outros componentes que possuem nivel.

Revision 1.3  2006/07/06 13:59:05  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:53  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_COMPONENTES."IMontaClassificacao.class.php" );
include_once ( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoClassificacao.class.php" );

$obIMontaClassificacao = new IMontaClassificacao;
switch ($_REQUEST["stCtrl"]) {
   case "MontaNiveisCombo":
       if ($_REQUEST['inCodCatalogo']) {
          $obFormulario   = new Formulario;
          $obIMontaClassificacao->setCodigoCatalogo( $_REQUEST['inCodCatalogo'] );
          $obIMontaClassificacao->geraFormulario( $obFormulario );
          $obFormulario->montaInnerHTML();
          echo $obFormulario->getHtml();
          $stJs = ' d.getElementById(\'spnClassificacao\').innerHTML = \''.$obFormulario->getHtml() .'\';';
       } else {
          $stJs = ' d.getElementById(\'spnClassificacao\').innerHTML = \'\';';
       }
    break;
    case "preencheProxComboClassificacao":

        $stNomeComboClassificacao = "inCodClassificacao_".( $_REQUEST["inPosicaoClassificacao"] - 1);
        $stChaveLocalClassificacao = $_REQUEST[$stNomeComboClassificacao];
        $arChaveLocalClassificacao = explode('-', $stChaveLocalClassificacao );
        $inPosicao = $_REQUEST["inPosicaoClassificacao"];
        $obIMontaClassificacao->setCodigoCatalogo     ( $_REQUEST["inCodCatalogo"] );
        $obIMontaClassificacao->setCodigoNivel        ( $arChaveLocalClassificacao[0] );
        $obIMontaClassificacao->setCodigoClassificacao( $arChaveLocalClassificacao[1] );
        $stReduzido = $arChaveLocalClassificacao[2];
        while( preg_match('/\.0+$/', $stReduzido ))
            $stReduzido = preg_replace('/\.0+$/', '', $stReduzido );
        $obIMontaClassificacao->setCodEstruturalReduzido( $stReduzido );
        $stJs = $obIMontaClassificacao->preencheProxComboClassificacao     ( $inPosicao , $_REQUEST["inNumNiveisClassificacao"] );
    break;
    case "preencheCombosClassificacao":
        $obIMontaClassificacao->setCodigoCatalogo       ( $_REQUEST["inCodCatalogo"]   );
        $obIMontaClassificacao->setCodEstruturalReduzido( $_REQUEST['stChaveClassificacao']);
        $stJs = $obIMontaClassificacao->preencheCombosClassificacao($_REQUEST['inNumNiveisClassificacao']);
    break;
}

if( $stJs)
    echo $stJs;
