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
* Arquivo de instância para popup
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 5003 $
$Name$
$Author: lizandro $
$Date: 2006-01-12 15:17:26 -0200 (Qui, 12 Jan 2006) $

Casos de uso: uc-01.06.98
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GA_PROT_NEGOCIO."RProcesso.class.php"  );
include_once(CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "BuscaProcessos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js" ;

include_once( $pgJS );

$stCtrl = $_REQUEST['stCtrl'];

$obRProcesso = new RProcesso;
$obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;

$obRConfiguracaoConfiguracao->setCodModulo( 5 );
$obRConfiguracaoConfiguracao->setExercicio( Sessao::getExercicio() );
$obRConfiguracaoConfiguracao->setParametro( 'mascara_assunto' );
$obRConfiguracaoConfiguracao->consultar();

$stMascaraAssunto = $obRConfiguracaoConfiguracao->getValor();
switch ($stCtrl) {
    case "buscaAssunto":
        $obRProcesso->setCodigoClassificacao( $_REQUEST['inCodClassificacao'] );

        $js .= "f.inCodAssunto.value = '';\n";
        $js .= "limpaSelect(f.inCodAssunto,0); \n";
        $js .= "f.inCodAssunto[0] = new Option('Selecione Assunto','', 'selected');\n";
        if ($_REQUEST["inCodClassificacao"]) {
            $obRProcesso->setCodigoClassificacao( $_REQUEST["inCodClassificacao"]);
            $obRProcesso->listarAssunto( $rsAssunto );
            $inContador = 1;

            while ( !$rsAssunto->eof() ) {
                $inCodAssunto = $rsAssunto->getCampo( "cod_assunto" );
                $stNomAssunto = $rsAssunto->getCampo( "nom_assunto" );
                $js .= "f.inCodAssunto.options[$inContador] = new Option('".$stNomAssunto."','".$inCodAssunto."'); \n";
                $inContador++;
                $rsAssunto->proximo();
            }
            $arMascaraProcesso = SistemaLegado::validaMascaraDinamica( $stMascaraAssunto, $_REQUEST['inCodClassificacao'] );
            $js .= "f.inClassAssunto.value = '".$arMascaraProcesso[1]."';\n";
        }
        SistemaLegado::executaIFrameOculto($js);
    break;
    case "preencheProcesso":
        $stTmp = $_REQUEST['inCodClassificacao'].".".$_REQUEST['inCodAssunto'];
        $arMascaraProcesso = SistemaLegado::validaMascaraDinamica( $stMascaraAssunto, $stTmp);
        $js .= "f.inClassAssunto.value = '".$arMascaraProcesso[1]."';\n";
        SistemaLegado::executaIFrameOculto($js);
    break;
    case "preencheCombos":
        $arTmp = explode(".",$_REQUEST['inClassAssunto']);
        $js .= "f.inCodClassificacao.selectedIndex = '".$arTmp[0]."';\n";
        $js .= "f.inCodAssunto.value = '';\n";
        $js .= "limpaSelect(f.inCodAssunto,0); \n";
        $js .= "f.inCodAssunto[0] = new Option('Selecione Assunto','', 'selected');\n";
        $obRProcesso->setCodigoClassificacao( $arTmp[0]);
        $obRProcesso->listarAssunto( $rsAssunto );
        $inContador = 1;
        while ( !$rsAssunto->eof() ) {
            $inCodAssunto = $rsAssunto->getCampo( "cod_assunto" );
            $stNomAssunto = $rsAssunto->getCampo( "nom_assunto" );
            $js .= "f.inCodAssunto.options[$inContador] = new Option('".$stNomAssunto."','".$inCodAssunto."'); \n";
            $inContador++;
            $rsAssunto->proximo();
        }
        $js .= "f.inCodAssunto.selectedIndex = '".$arTmp[1]."';\n";
        SistemaLegado::executaIFrameOculto($js);
    break;
}
