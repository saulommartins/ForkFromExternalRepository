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

    * $Id: OCAtividades.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.15

*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GT_CEM_NEGOCIO."RCEMRelatorioAtividades.class.php" );

// INSTANCIA OBJETO
$obRRelatorio              = new RRelatorio;
$obRCEMRelatorioAtividades = new RCEMRelatorioAtividades;
$arFiltroSessao = Sessao::read( "filtroRelatorio" );

// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$obRCEMRelatorioAtividades->setNomAtividade      ( $arFiltroSessao['stNomAtividade']       );
$obRCEMRelatorioAtividades->setCodInicio         ( $arFiltroSessao['inCodInicio']          );
$obRCEMRelatorioAtividades->setCodTermino        ( $arFiltroSessao['inCodTermino']         );
$obRCEMRelatorioAtividades->setCodVigencia       ( $arFiltroSessao['inCodVigencia']  );
$obRCEMRelatorioAtividades->setOrder             ( $arFiltroSessao['stOrder']              );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCEMRelatorioAtividades->geraRecordSet( $rsAtividades );
Sessao::write( "sessao_transf5", $rsAtividades );

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAtividades.php" );
?>
