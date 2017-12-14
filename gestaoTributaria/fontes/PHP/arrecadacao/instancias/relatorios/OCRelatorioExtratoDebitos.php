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
    * Frame Oculto para relatorio de Extrato de Debitos
    * Data de Criação: 13/07/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: OCRelatorioExtratoDebitos.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.2  2007/08/01 13:56:54  dibueno
Bug#9793#

Revision 1.1  2007/07/16 16:03:57  dibueno
Bug #9659#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."FARRRelatorioExtratoDebitos.class.php"                        );

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;

// SETA ELEMENTOS DO FILTRO
$stFiltro = "";

switch ($stCtrl) {

    case "PreencheSpanOrigem":

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
        $valor = "OEM";

        $stFiltroSQL    = $_REQUEST['stSQL'];
        $stFiltroINNER  = $_REQUEST['stINNER'];

        $boFARRRelatorioExtratoDebitos = new FARRRelatorioExtratoDebitos;
        $boFARRRelatorioExtratoDebitos->recuperaRelatorioOrigem ( $rsListaOrigem, $stFiltroINNER, $stFiltroSQL );

        $table = new Table();
        $table->setRecordset( $rsListaOrigem );
        $table->setSummary('Resumo de Parcelas em Aberto');

        $table->Head->addCabecalho( 'Exercício' , 10  );
        $table->Head->addCabecalho( 'Origem' , 30  );
        $table->Head->addCabecalho( 'Qtde de Parcelas' , 10  );
        $table->Head->addCabecalho( '' , 50  );

        $table->Body->addCampo( 'exercicio', "C" );
        $table->Body->addCampo( 'origem', "E");
        $table->Body->addCampo( 'qtde', "C" );
        $table->Body->addCampo( '', "D" );

        $table->montaHTML();
        $valor = $table->getHTML();

        $valor = str_replace( "\n" ,"" ,$valor );
        $valor = str_replace( "  " ,"" ,$valor );
        $valor = str_replace( "'","\\'",$valor );

        $stJs = "d.getElementById('spnDetalhes').innerHTML= '". $valor ."';\n";

    break;

}

SistemaLegado::executaFrameOculto($stJs);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioExtratoDebitos.php" );

?>
