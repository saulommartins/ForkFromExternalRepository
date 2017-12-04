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
    * Data de Criação   : 15/12/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    * $Id: OCResumoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                             );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaRelatorioResumoReceita.class.php"  );

switch ($_GET['stCtrl']) {
    case 'mostraSpanReceita':
        switch ($_GET['stTipoReceita']) {
            case 'orcamentaria':
                include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpReceita.class.php" );
                $obIIntervaloPopUpReceita = new IIntervaloPopUpReceita();
                $obIIntervaloPopUpReceita->obIPopUpReceitaInicial->setUsaFiltro ( true );
                $obIIntervaloPopUpReceita->obIPopUpReceitaInicial->obCampoCod->setName('inReceitaInicial');

                $obIIntervaloPopUpReceita->obIPopUpReceitaFinal->setUsaFiltro ( true );
                $obIIntervaloPopUpReceita->obIPopUpReceitaFinal->obCampoCod->setName('inReceitaFinal');

                $obFormulario = new Formulario();
                $obFormulario->addComponente($obIIntervaloPopUpReceita);
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();

                echo "d.getElementById('spnReceitas').innerHTML = '".$stHTML."';";
            break;

            case 'extra':
                include_once( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php");
                $obIPopUpContaAnalitica = new IIntervaloPopUpContaAnalitica;
                $obIPopUpContaAnalitica->setRotulo ( 'Conta de Receita');
                $obIPopUpContaAnalitica->setTitle ( 'Informe o código da conta de receita.');
                $obIPopUpContaAnalitica->obIPopUpContaAnaliticaInicial->setTipoBusca( 'tes_arrecadacao_extra_receita' );
                $obIPopUpContaAnalitica->obIPopUpContaAnaliticaInicial->obCampoCod->setName('inReceitaInicial');

                $obIPopUpContaAnalitica->obIPopUpContaAnaliticaFinal->setTipoBusca( 'tes_arrecadacao_extra_receita' );
                $obIPopUpContaAnalitica->obIPopUpContaAnaliticaFinal->obCampoCod->setName('inReceitaFinal');

                $obFormulario = new Formulario();
                $obFormulario->addComponente($obIPopUpContaAnalitica);
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();

                echo "d.getElementById('spnReceitas').innerHTML = '".$stHTML."';";
            break;
            default:
                echo "d.getElementById('spnReceitas').innerHTML = '';";
            break;
        }
    break;

    default:

        $obRRelatorio                          = new RRelatorio;
        $obRTesourariaRelatorioResumoReceita = new RTesourariaRelatorioResumoReceita;

        $stEntidade = "";

        $arFiltro = Sessao::read('filtroRelatorio');

        foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $key => $valor) {
            $stEntidade.= $valor . ",";
        }

        $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );

        if (trim($arFiltro['inReceitaInicial']) == "") {
            $arFiltro['inReceitaInicial'] = 0;
        }
        if (trim($arFiltro['inReceitaFinal']) == "") {
            $arFiltro['inReceitaFinal'] = 0;
        }

        if (trim($arFiltro['inContaBancoInicial']) == "") {
            $arFiltro['inContaBancoInicial'] = 0;
        }
        if (trim($arFiltro['inContaBancoFinal']) == "") {
            $arFiltro['inContaBancoFinal'] = 0;
        }

        if (trim($arFiltro['inCodRecurso']) == "") {
            $inCodRecurso = '999999'; // Existe o Recurso de cod_recurso 0 em algumas bases.
        } else {
            $inCodRecurso = $arFiltro['inCodRecurso'];
        }

        $obRTesourariaRelatorioResumoReceita->setEntidade           ( $stEntidade                            );
        $obRTesourariaRelatorioResumoReceita->setExercicio          ( $arFiltro['stExercicio']         );
        $obRTesourariaRelatorioResumoReceita->setDataInicial        ( $arFiltro['stDataInicial']       );
        $obRTesourariaRelatorioResumoReceita->setDataFinal          ( $arFiltro['stDataFinal']         );
        $obRTesourariaRelatorioResumoReceita->setTipoRelatorio      ( $arFiltro['stTipoRelatorio']     );
        $obRTesourariaRelatorioResumoReceita->setReceitaInicial     ( $arFiltro['inReceitaInicial']    );
        $obRTesourariaRelatorioResumoReceita->setReceitaFinal       ( $arFiltro['inReceitaFinal']      );
        $obRTesourariaRelatorioResumoReceita->setContaBancoInicial  ( $arFiltro['inContaBancoInicial'] );
        $obRTesourariaRelatorioResumoReceita->setContaBancoFinal    ( $arFiltro['inContaBancoFinal']   );
        $obRTesourariaRelatorioResumoReceita->setCodRecurso         ( $inCodRecurso                          );
        if ($arFiltro['inCodUso'] != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
            $obRTesourariaRelatorioResumoReceita->setDestinacaoRecurso ( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
        }

        if($arFiltro['inCodDetalhamento'] )
            $obRTesourariaRelatorioResumoReceita->setCodDetalhamento ($arFiltro['inCodDetalhamento']);

        $obRTesourariaRelatorioResumoReceita->setTipoReceita        ( $arFiltro['stTipoReceita']       );

        $obRTesourariaRelatorioResumoReceita->geraRecordSet( $rsResumoReceita );

        Sessao::write('arDados', $rsResumoReceita);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioResumoReceita.php" );
        break;
}

?>
