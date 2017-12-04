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
    * Frame Oculto para popup logradouro
    * Data de CriaÃ§Ã£o   : 28/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: OCContadores.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.02.14

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GT_CEM_NEGOCIO."RCEMRelatorioContadores.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Contadores";

// INSTANCIA OBJETO
$obRRelatorio              = new RRelatorio;
$obRCEMRelatorioContadores = new RCEMRelatorioContadores;

$arFiltroSessao = Sessao::read( "filtroRelatorio" );
// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$obRCEMRelatorioContadores->setNomContador           ( $arFiltroSessao['stNomCGM']                 );
$obRCEMRelatorioContadores->setCodInicio             ( $arFiltroSessao['inCodInicio']              );
$obRCEMRelatorioContadores->setCodTermino            ( $arFiltroSessao['inCodTermino']             );
$obRCEMRelatorioContadores->setCodInicioCadEconomico ( $arFiltroSessao['inCodInicioCadEconomico']  );
$obRCEMRelatorioContadores->setCodTerminoCadEconomico( $arFiltroSessao['inCodTerminoCadEconomico'] );
$obRCEMRelatorioContadores->setOrder                 ( $arFiltroSessao['stOrder']                  );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCEMRelatorioContadores->geraRecordSet( $rsContadores );
if ($arFiltroSessao["stTipoRelatorio"] == "analitico") {
    $stContador = $rsContadores->getCampo( "contador" );
    $rsContadores->proximo();
    while ( !$rsContadores->Eof() ) {
        if ( $stContador == $rsContadores->getCampo("contador") ) {
            $rsContadores->setCampo( "contador", "" );
            $rsContadores->setCampo( "num_registro", "" );
            $rsContadores->setCampo( "endereco", "" );
            $rsContadores->setCampo( "fone_comercial", "" );
        }else
            $stContador = $rsContadores->getCampo("contador");

        $rsContadores->proximo();
    }

    $rsContadores->setPrimeiroElemento();
} else {
    $arElementos = $rsContadores->getElementos();
    $arContador = array();
    $arControle = array();
    $inPosicao = 0;
    for ( $inX=0; $inX<count( $arElementos ); $inX++ ) {
        if (!$arControle[$arElementos[$inX]["contador"]]) {
            $inPosicao++;
            $arControle[$arElementos[$inX]["contador"]] = $inPosicao;
            $arContador[] = array(
                "contador" => $arElementos[$inX]["contador"],
                "num_registro" => $arElementos[$inX]["num_registro"],
                "endereco" => $arElementos[$inX]["endereco"],
                "fone_comercial" => $arElementos[$inX]["fone_comercial"]
            );
        }
    }

    $rsContadores->preenche( $arContador );
}

Sessao::write( "sessao_transf5", $rsContadores );

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioContadores.php" );
?>
