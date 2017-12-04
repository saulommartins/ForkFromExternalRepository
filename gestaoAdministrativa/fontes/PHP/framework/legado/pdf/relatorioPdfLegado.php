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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

set_time_limit(0);
include_once '../../include/valida.inc.php';
include_once (CAM_FRAMEWORK."legado/funcoesLegado.lib.php"           );
include_once (CAM_FRAMEWORK."legado/mascarasLegado.lib.php"          );
include_once (CAM_FRAMEWORK.'legado/pdf/arquivoPdfLegado.class.php'  );
include_once (CAM_FRAMEWORK.'legado/pdf/relatorioPdfLegado.class.php');

/********************************************************
Variáveis Recebidas pelo relatorioPDF.php
$sAcaoPDF       = pode ser: enviar, imprimir, salvar
                  a acao salvar é a opção padrão caso nenhuma
                  das três seja selecionada
$sScriptXML     = caminho completo do script xml, exemplo
                  "../administracao/relatorios/auditoria.xml"
$sSQL           = sql que vem da pagina que solicitou o pdf
$sTitulo        = Titulo do Relatorio
$sSubTitulo     = Subtitulo do Relatorio
$sFilaImpressao = Fila de Impressão do relatório
$iCopias        = Quantidade de cópias
*********************************************************/

$relPDF = new relatorioPdfLegado;
$relPDF->BANCODEDADOS['SQL']      = $_REQUEST['sSQL'];
$relPDF->PROPRIEDADES['TITULO']   = $_REQUEST['sTitulo'];
$relPDF->PROPRIEDADES['SUBTITULO']= $_REQUEST['sSubTitulo'];
$relPDF->sFilaImpressao         = $_REQUEST['sFilaImpressao'];
$relPDF->sAcaoPDF               = $_REQUEST['sAcaoPDF'];
$relPDF->sParametros            = $_REQUEST['sParametros'];
$relPDF->iCopias                = $_REQUEST['iCopias'];

$relPDF->abreScript($_REQUEST['sScriptXML']);

$relPDF->carregaDados();

switch ($_REQUEST['sAcaoPDF']) {
    case 'enviar':
        $relPDF->enviaPDF();
        break;
    case 'imprimir':
        include_once '../../../framework/include/cabecalho.inc.php';
        $relPDF->imprimePDF();
        break;
    default:
        $relPDF->salvaPDF();
        break;
}
?>
