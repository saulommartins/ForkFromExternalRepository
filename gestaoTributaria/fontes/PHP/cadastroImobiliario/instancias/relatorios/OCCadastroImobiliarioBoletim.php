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
    * Frame Oculto para relatorio BCI
    * Data de Criação: 22/08/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCCadastroImobiliarioBoletim.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.26
*/

/*
$Log$
Revision 1.3  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                               );
include_once( CAM_GT_CIM_NEGOCIO."RCIMRelatorioCadastroImobiliario.class.php" );

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;
$obRCIMRelatorioCadastroImobiliario = new RCIMRelatorioCadastroImobiliario;

$arFiltro = Sessao::read('filtroRelatorio');
// SETA ELEMENTOS DO FILTRO
$obRCIMRelatorioCadastroImobiliario->setCodInicioInscricao   ( $arFiltro['inCodInicioInscricao']       );
$obRCIMRelatorioCadastroImobiliario->setCodInicioLocalizacao ( $arFiltro['inCodInicioLocalizacao']     );
$obRCIMRelatorioCadastroImobiliario->setCodInicioBairro      ( $arFiltro['inCodInicioBairro']          );
$obRCIMRelatorioCadastroImobiliario->setCodInicioLogradouro  ( $arFiltro['inCodInicioLogradouro']      );

$obRCIMRelatorioCadastroImobiliario->setCodTerminoInscricao  ( $arFiltro['inCodTerminoInscricao']      );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoLocalizacao( $arFiltro['inCodTerminoLocalizacao']    );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoBairro     ( $arFiltro['inCodTerminoBairro']         );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoLogradouro ( $arFiltro['inCodTerminoLogradouro']     );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioCadastroImobiliario->listarBoletimCadastroImobiliario( $rsCadastroImobiliario );

$arDados = $rsCadastroImobiliario->getElementos();
$inTotalDados = count( $arDados );
$arTmpDados = array();
$arTmpConfrontacoes = array();
$inPosLivre = 0;
for ($inX=0; $inX<$inTotalDados; $inX++) {
    $boIncluir = true;
    for ($inY=0; $inY<$inPosLivre; $inY++) {
        if ($arTmpDados[$inY]["inscricao_municipal"] == $arDados[$inX]["inscricao_municipal"]) {
            if ($arTmpDados[$inY]["numcgm_proprietario"] == $arDados[$inX]["numcgm_proprietario"]) { //podem existir mais de uma confrontacao
                //armazenando dados das demais confrontacoes
                $inInscricao = $arTmpDados[$inY]["inscricao_municipal"];
                $inPosicaoLivre = $arTmpConfrontacoes[$inInscricao]["total_conf"];
                $arTmpConfrontacoes[$inInscricao][$inPosicaoLivre]["conf_lot_ponto_cardeal"] = $arDados[$inX]["conf_lot_ponto_cardeal"];
                $arTmpConfrontacoes[$inInscricao][$inPosicaoLivre]["conf_lot_metragem"] = $arDados[$inX]["conf_lot_metragem"];
                $arTmpConfrontacoes[$inInscricao][$inPosicaoLivre]["conf_lot_especificar"] = $arDados[$inX]["conf_lot_especificar"];
                $arTmpConfrontacoes[$inInscricao][$inPosicaoLivre]["conf_principal"] = $arDados[$inX]["conf_principal"];
                $arTmpConfrontacoes[$inInscricao][$inPosicaoLivre]["conf_ativa"] = "[  ]";
                $arTmpConfrontacoes[$inInscricao]["total_conf"]++;
            }

            $boIncluir = false;
            break;
        }
    }

    if ($boIncluir) {
        $inInscricao = $arDados[$inX]["inscricao_municipal"];
        $arTmpConfrontacoes[$inInscricao]["total_conf"] = 1;
        $arTmpConfrontacoes[$inInscricao][0]["conf_lot_ponto_cardeal"] = $arDados[$inX]["conf_lot_ponto_cardeal"];
        $arTmpConfrontacoes[$inInscricao][0]["conf_lot_metragem"] = $arDados[$inX]["conf_lot_metragem"];
        $arTmpConfrontacoes[$inInscricao][0]["conf_lot_especificar"] = $arDados[$inX]["conf_lot_especificar"];
        $arTmpConfrontacoes[$inInscricao][0]["conf_principal"] = $arDados[$inX]["conf_principal"];
        $arTmpConfrontacoes[$inInscricao][0]["conf_ativa"] = "[  ]";

        $arTmpDados[$inPosLivre] = $arDados[$inX];
        $inPosLivre++;
    }
}

Sessao::write('rsImoveis'       , $arTmpDados);
Sessao::write('rsConfrontacoes' , $arTmpConfrontacoes);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCadastroImobiliarioBoletim.php" );
?>
