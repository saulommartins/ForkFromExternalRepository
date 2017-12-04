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
* Oculto
* Data de Criação: 25/05/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage oculto

$Revision: 30668 $
$Name$
$Author: cleisson $
$Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

* Casos de uso: uc-02.05.11
                uc-02.05.12

*/

/*
$Log$
Revision 1.4  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
if ($_REQUEST['inCodModelo']) {
   $sessao->filtro['inCodModelo'] = $_REQUEST['inCodModelo'];
}
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelos".$sessao->filtro['inCodModelo'].".class.php"  );
include_once( CAM_FW_PDF."RRelatorio.class.php"           );

include_once 'JSModelosExecutivo.js';

$obRRelatorio       = new RRelatorio;
$RegraAux           = 'RLRFRelatorioModelos'.$sessao->filtro['inCodModelo'];
$obRegra            = new $RegraAux;

$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

function listarMes()
{
}

function verificaMes($mesInformado)
{
    switch ($mesInformado) {
        case 1:
            $mesExtenso = "Janeiro";
        break;
        case 2:
            $mesExtenso = "Fevereiro";
        break;
        case 3:
            $mesExtenso = "Março";
        break;
        case 4:
            $mesExtenso = "Abril";
        break;
        case 5:
            $mesExtenso = "Maio";
        break;
        case 6:
            $mesExtenso = "Junho";
        break;
        case 7:
            $mesExtenso = "Julho";
        break;
        case 8:
            $mesExtenso = "Agosto";
        break;
        case 9:
            $mesExtenso = "Setembro";
        break;
        case 10:
            $mesExtenso = "Outubro";
        break;
        case 11:
            $mesExtenso = "Novembro";
        break;
        case 12:
            $mesExtenso = "Dezembro";
        break;
    }
return $mesExtenso;
}

function verificaDia($mes,$ano)
{
    if ($mes == "1" || $mes == "3" || $mes == "5" || $mes == "7" || $mes == "8" || $mes == "10" || $mes == "12") {
        $dia = 31;
    } elseif ($mes == "4" || $mes == "6" || $mes == "9" || $mes == "11") {
        $dia = 30;
    } else {
        if (date('L',mktime(0,0,0,$mes,1,$ano))) {
            $dia = 29;
        } else {
            $dia = 28;
        }
    }

    return $dia;
}

switch ($stCtrl) {
    case "listarMes":
        $stCombo  = "inMes";
        $stJs .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        for ($i=1; $i <= 12; $i++) {
            $stDesc = verificaMes($i);
            $stJs .= "f.$stCombo.options[$i] = new Option('".$stDesc."','".$i."',''); \n";
        }
    break;

    default:
        //seta elementos do filtro para ENTIDADE
        if ($sessao->filtro['inCodEntidade'] != "") {
            $inCount = 0;
            foreach ($sessao->filtro['inCodEntidade'] as $key => $valor) {
                $stEntidade .= $valor.",";
                $inCount++;
            }
            $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
        } else {
            $stEntidade .= $sessao->filtro['stTodasEntidades'];
        }

        $stFiltro = "";

            $obRegra->setFiltro             ( $stFiltro );
            $obRegra->setCodEntidade        ( $stEntidade );
            $obRegra->setCodModelo          ( $sessao->filtro['inCodModelo']);
            $obRegra->setExercicio          ( Sessao::getExercicio() );
            $obRegra->setDataInicial        ( date("d/m/Y",mktime(0, 0, 0, $sessao->filtro['inMes']-11, 1, Sessao::getExercicio())));
            $obRegra->setDataFinal          ( date("d/m/Y",mktime(0, 0, 0, $sessao->filtro['inMes'], verificaDia($sessao->filtro['inMes'],Sessao::getExercicio()) , Sessao::getExercicio())) );
            $obRegra->setTipoValorDespesa   ( $sessao->filtro['stTipoDespesa'] );

            $sessao->transf4     = array();
            $sessao->transf4[1]  = date("d/m/Y",mktime(0, 0, 0, $sessao->filtro['inMes']-11, 1, Sessao::getExercicio()));
            $sessao->transf4[2]  = date("d/m/Y",mktime(0, 0, 0, $sessao->filtro['inMes'], verificaDia($sessao->filtro['inMes'],Sessao::getExercicio()) , Sessao::getExercicio()));

            switch ($sessao->filtro['inCodModelo']) {
                case 10 :
                    $obRegra->geraRecordSet( $rsRelatorio,$rsRelatorioTotal,$stOrder );
                    $sessao->transf5     = array();
                    $sessao->transf5[1]  = $rsRelatorio;
                    $sessao->transf5[2]  = $rsRelatorioTotal;
                    break;
                case 11 :
                    $obRegra->geraRecordSet( $rsRelatorio,$rsRelatorioTotal );
                    $sessao->transf5     = array();
                    $sessao->transf5[1]  = $rsRelatorio;
                    $sessao->transf5[3]  = $rsRelatorioTotal;
                    break;
                case 12 :
                    $obRegra->geraRecordSet( $rsRelatorio,$rsRelatorio1,$rsRelatorioTotal );
                    $sessao->transf5     = array();
                    $sessao->transf5[1]  = $rsRelatorio;
                    $sessao->transf5[2]  = $rsRelatorio1;
                    $sessao->transf5[3]  = $rsRelatorioTotal;
                    break;
                case 13 :
                    $obRegra->geraRecordSet( $rsRelatorio,$rsRelatorio1,$rsRelatorio2,$rsRelatorio3 );
                    $sessao->transf5     = array();
                    $sessao->transf5[1]  = $rsRelatorio;
                    $sessao->transf5[2]  = $rsRelatorio1;
                    $sessao->transf5[3]  = $rsRelatorio2;
                    $sessao->transf5[4]  = $rsRelatorio3;
                    break;
                break;
                case 14 :
                    $obRegra->geraRecordSet( $rsRelatorio,$rsRelatorio1, $stOrder );
                    $sessao->transf5     = array();
                    $sessao->transf5[1]  = $rsRelatorio;
                    $sessao->transf5[2]  = $rsRelatorio1;
                    break;
                break;
            }
            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioModelos".$sessao->filtro['inCodModelo'].".php");
    break;

}

if($stJs)
   SistemaLegado::executaFrameOculto($stJs);

?>
