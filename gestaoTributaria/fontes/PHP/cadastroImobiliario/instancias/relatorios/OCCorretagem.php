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
    * Página de processamento oculto para o relatório de corretagem
    * Data de Criação   : 30/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCCorretagem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.21
*/

/*
$Log$
Revision 1.6  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMCorretagem.class.php"              );
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GT_CIM_NEGOCIO."RCIMRelatorioCorretagem.class.php" );

//Define o nome dos arquivos PHP
$stPrograma          = "Corretagem";
$pgFilt              = "FL".$stPrograma.".php";

// INSTANCIA OBJETO
$obRCIMCorretagem          = new RCIMCorretagem;
$obRRelatorio              = new RRelatorio;
$obRCIMRelatorioCorretagem = new RCIMRelatorioCorretagem;

$arFiltro = Sessao::read('filtroRelatorio');
// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$obRCIMRelatorioCorretagem->obRCIMCorretagem->obRCGM->setNomCGM( $arFiltro['stNomCGM'] );
$obRCIMRelatorioCorretagem->setCGMInicio     ( $arFiltro['inCGMInicio']      );
$obRCIMRelatorioCorretagem->setCGMTermino    ( $arFiltro['inCGMTermino']     );
$obRCIMRelatorioCorretagem->setOrder         ( $arFiltro['stOrder']          );
$obRCIMRelatorioCorretagem->setTipoCorretagem( $arFiltro['stTipoCorretagem'] );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioCorretagem->geraRecordSet( $rsCorretagem );
Sessao::write('sessao_transf5', $rsCorretagem);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCorretagem.php" );

?>
