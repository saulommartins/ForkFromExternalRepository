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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 28/11/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 32100 $
    $Name$
    $Autor:$
    $Date: 2007-04-12 11:38:51 -0300 (Qui, 12 Abr 2007) $

    * Casos de uso: uc-02.04.07
*/

/*
$Log$
Revision 1.13  2007/04/12 14:38:51  vitor
8933

Revision 1.12  2007/02/16 17:09:40  cako
Bug #8400#

Revision 1.11  2007/01/12 11:53:54  luciano
Bug #7781#

Revision 1.10  2006/07/05 20:39:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_TES_MAPEAMENTO."TTesourariaBoletim.class.php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'habilitaDesabilitaContasSemMovimento':
        if ($_GET['stTipoEmissao'] == 'boletim') {
            $stJs  = "jQuery('#boMovimentacaoContaSim').removeAttr('disabled');\n";
            $stJs .= "jQuery('#boMovimentacaoContaNao').removeAttr('disabled');\n";
        } else {
            $stJs  = "jQuery('#boMovimentacaoContaSim').attr('disabled', 'disabled');\n";
            $stJs .= "jQuery('#boMovimentacaoContaNao').attr('disabled', 'disabled');\n";
        }
        break;
    case 'buscaBoletimPorData':
        if (!empty($_GET['inCodEntidade']) && !empty($_GET['stDtBoletim'])) {
            $filtro  = " WHERE cod_entidade = ".$_GET['inCodEntidade'];
            $filtro .= "   AND dt_boletim   = to_date('".$_GET['stDtBoletim']."','DD/MM/YYYY')";

            $obTTesourariaBoletim = new TTesourariaBoletim();
            $obTTesourariaBoletim->recuperaTodos($rsBoletim, $filtro);

            if ($rsBoletim->getNumLinhas() > 0) {
                $stJs = "jQuery('#inCodBoletim').val('".$rsBoletim->getCampo('cod_boletim')."')";
            }
        }
    break;
    default:
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
        include_once( CAM_FW_PDF."RRelatorio.class.php"                                 );
        include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioEmitirBoletim.class.php"  );
        $obRRelatorio                        = new RRelatorio;
        $obRTesourariaRelatorioEmitirBoletim = new RTesourariaRelatorioEmitirBoletim;
        $stEntidade = "";

        $arFiltro = Sessao::read('filtroRelatorio');
        $stEntidade = $arFiltro['inCodEntidade'];
        $obRTesourariaRelatorioEmitirBoletim->setSemMovimentacao($arFiltro['boMovimentacaoConta']);

        if (is_array($stEntidade)) {
            foreach ($stEntidade as $key => $value) {
                $stEntidades .= $value.',';
            }
            $stEntidade = substr($stEntidades,0,strlen($stEntidades)-1);
        }

        if (!$arFiltro['inCodBoletim']) {
            $obRTesourariaRelatorioEmitirBoletim->obRTesourariaBoletim->setDataBoletim($arFiltro['stDtBoletim']);
            $obRTesourariaRelatorioEmitirBoletim->obRTesourariaBoletim->setExercicio(Sessao::getExercicio());
            $obRTesourariaRelatorioEmitirBoletim->obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($stEntidade);
            $obRTesourariaRelatorioEmitirBoletim->obRTesourariaBoletim->listar($rsRecordSet);

            if ($rsRecordSet->getNumLinhas() > 1 ) {
                while (!$rsRecordSet->eof()) {
                    $inCodBoletim .= $rsRecordSet->getCampo('cod_boletim').",";
                    $rsRecordSet->proximo();
                }
                $inCodBoletim = substr($inCodBoletim,0,strlen($inCodBoletim)-1);
            } else {
                $inCodBoletim = $rsRecordSet->getCampo('cod_boletim');
                $arFiltro['inCodBoletim'] =  $rsRecordSet->getCampo('cod_boletim');
            }
        } else {
            $inCodBoletim = $arFiltro['inCodBoletim'];
        }

        $obRTesourariaRelatorioEmitirBoletim->setEntidade    ( $stEntidade                    );
        $obRTesourariaRelatorioEmitirBoletim->setTipoEmissao ( $arFiltro['stTipoEmissao']     );
        $obRTesourariaRelatorioEmitirBoletim->setCodTerminal ( $arFiltro['inCodTerminal']     );
        $obRTesourariaRelatorioEmitirBoletim->setDtBoletim   ( $arFiltro['stDtBoletim']       );
        $obRTesourariaRelatorioEmitirBoletim->setCodBoletim  ( $inCodBoletim                  );
        $obRTesourariaRelatorioEmitirBoletim->setCgmUsuario  ( $arFiltro['inNumCgm']          );
        $obRTesourariaRelatorioEmitirBoletim->setExercicio   ( Sessao::getExercicio()         );
        $obRTesourariaRelatorioEmitirBoletim->obFTesourariaEmitirBoletim->setDado('botcems', 'false' );
        if (Sessao::getExercicio() > '2012') {
            $obRTesourariaRelatorioEmitirBoletim->obFTesourariaEmitirBoletim->setDado('botcems', 'true' );
        }

        if(count($arFiltro['inCodEntidade']) == 1)
            $obRTesourariaRelatorioEmitirBoletim->setIncluirAssinatura( $arFiltro['stIncluirAssinaturas'] );
        else
            $obRTesourariaRelatorioEmitirBoletim->setIncluirAssinatura("nao");

        $obRTesourariaRelatorioEmitirBoletim->geraRecordSet( $arRecordSet );

        Sessao::write('arDados',$arRecordSet);
        Sessao::write('filtroRelatorio',$arFiltro);

        if ($arFiltro['stTipoEmissao'] == "caixa") {
            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmitirTerminal.php" );
        } else {
            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioEmitirBoletim.php" );
        }
    break;
}

if (!empty($stJs)) {
    echo $stJs;
}

?>
