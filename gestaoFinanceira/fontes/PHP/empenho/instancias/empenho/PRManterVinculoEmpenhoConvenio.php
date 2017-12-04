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
    * Arquivo de Processamento para vínculo Empenho-Convênio.
    * Data de Criação: 17/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.38

    $Id: PRManterVinculoEmpenhoConvenio.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoConvenio.class.php" 							  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoConvenio";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case "incluir":

        $obErro = new Erro;
        //Inclui os itens da lista de empenhos
        if ( Sessao::read('elementos') != "" ) {

            $rsRecordset = new RecordSet;
            $rsRecordset->preenche(Sessao::read('elementos'));

            if ($rsRecordset) {
                while ( !$rsRecordset->eof() ) {
                    $obTEmpenhoEmpenhoConvenio = new TEmpenhoEmpenhoConvenio();
                    $stFiltro  = " WHERE empenho_convenio.exercicio    = '".$rsRecordset->getCampo('exercicio')."'   \n";
                    $stFiltro .= "   AND empenho_convenio.cod_entidade =  ".$rsRecordset->getCampo('cod_entidade'). "\n";
                    $stFiltro .= "   AND empenho_convenio.cod_empenho  =  ".$rsRecordset->getCampo('cod_empenho').  "\n";
                    $obTEmpenhoEmpenhoConvenio->recuperaTodos($rsEmpenhos, $stFiltro, "");

                    if ( $rsEmpenhos->getNumLinhas() < 0 ) {
                           $obTEmpenhoEmpenhoConvenio->setDado( "exercicio"    , $rsRecordset->getCampo("exercicio") 	 );
                        $obTEmpenhoEmpenhoConvenio->setDado( "cod_entidade" , $rsRecordset->getCampo('cod_entidade') );
                        $obTEmpenhoEmpenhoConvenio->setDado( "cod_empenho"  , $rsRecordset->getCampo('cod_empenho')  );
                          $obTEmpenhoEmpenhoConvenio->setDado( "num_convenio" , $_REQUEST['numConvenio'] 				 );
                           $obErro = $obTEmpenhoEmpenhoConvenio->inclusao();
                    }
                       $rsRecordset->proximo();
                }
            }
        }

        //Exclui os itens que foram retirados da lista
        if (Sessao::read('elementos_excluidos') != "") {
            $rsRecordset = new RecordSet;
            $rsRecordset->preenche(Sessao::read('elementos_excluidos'));

            while ( !$rsRecordset->eof() ) {
                $obTEmpenhoEmpenhoConvenio = new TEmpenhoEmpenhoConvenio();
                $stFiltro  = " WHERE empenho_convenio.exercicio    = '".$rsRecordset->getCampo('exercicio')."'   \n";
                $stFiltro .= "   AND empenho_convenio.cod_entidade =  ".$rsRecordset->getCampo('cod_entidade'). "\n";
                $stFiltro .= "   AND empenho_convenio.cod_empenho  =  ".$rsRecordset->getCampo('cod_empenho').  "\n";
                $obTEmpenhoEmpenhoConvenio->recuperaTodos($rsEmpenhos, $stFiltro, "");

                if ( $rsEmpenhos->getNumLinhas() > 0 ) {
                    $obTEmpenhoEmpenhoConvenio->setDado( "cod_entidade" , $rsEmpenhos->getCampo('cod_entidade') );
                       $obTEmpenhoEmpenhoConvenio->setDado( "cod_empenho"  , $rsEmpenhos->getCampo('cod_empenho')  );
                       $obTEmpenhoEmpenhoConvenio->setDado( "exercicio"    , $rsEmpenhos->getCampo('exercicio') 	);
                       $obErro = $obTEmpenhoEmpenhoConvenio->exclusao();
                }
                $rsRecordset->proximo();
            }
        }

        if ( Sessao::read('elementos_excluidos') == "" and Sessao::read('elementos') == "" ) {
            $obErro->setDescricao( "Efetue a inclusão e/ou exclusão de empenho referente ao convênio relacionado!" );
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgFilt."?stAcao=".$stAcao,"Convênio: ".$_REQUEST['numConvenio'] ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

        break;
}
?>
