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
    * Página de processamento oculto para o relatório de bairros
    * Data de Criação: 21/06/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCConsultaImovelRelatorio.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.5  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_RELATORIO."RCIMRelatorioImovelConsulta.class.php"     );
include_once( CAM_RELATORIO."RCIMRelatorioImovelConsultaLote.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RRelatorio.class.php"                          );

$stPrograma          = "ConsultaImovel";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";
include_once( $pgJs );

// INSTANCIA OBJETO
$obRRelatorio                      = new RRelatorio;
$obRCIMRelatorioImovelConsulta     = new RCIMRelatorioImovelConsulta;
$obRCIMRelatorioImovelConsultaLote = new RCIMRelatorioImovelConsultaLote( $sessao->filtro['stTipoLote'] );

//SETA FILTROS PARA O RELATÓRIO
$obRCIMRelatorioImovelConsultaLote->obRCIMLote->setCodigoLote                          ( $sessao->filtro['inCodLote']        );
$obRCIMRelatorioImovelConsultaLote->obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $sessao->filtro['inCodLocalizacao'] );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioImovelConsultaLote->geraRecordSet( $rsLote, $rsLoteConfrontacoes );
Sessao::write('lote', $rsLote);
Sessao::write('lote_confrontacoes', $rsLoteConfrontacoes);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioImovelConsulta.php" );
?>
