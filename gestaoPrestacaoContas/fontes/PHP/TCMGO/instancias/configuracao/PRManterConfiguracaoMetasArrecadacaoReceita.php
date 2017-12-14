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
  * Página de Processamento da Configuração de Metas de Arrecadacao da Receita
  * Data de Criação: 22/01/2015

  * @author Analista: Ane Caroline
  * @author Desenvolvedor: Lisiane Morais

  * @ignore
  *
  * $Id:$
  *
  * $Revision:$
  * $Author:$
  * $Date:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOMetasArrecadacaoReceita.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasArrecadacaoReceita";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$obErro = new Erro;

switch ($stAcao) {
    default:
        $boFlagTransacao = false;
        $obTransacao = new Transacao;
        $rsTTCMGOMetasArrecadacaoReceita = new RecordSet();
        $obTTCMGOMetasArrecadacaoReceita = new TTCMGOMetasArrecadacaoReceita();
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

        if (!$obErro->ocorreu()) {
            $obTTCMGOMetasArrecadacaoReceita->setDado('exercicio'                    , $request->get('stExercicio'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_1_bi'        , $request->get('valorMetaArrecadacao1Bi'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_2_bi'        , $request->get('valorMetaArrecadacao2Bi'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_3_bi'        , $request->get('valorMetaArrecadacao3Bi'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_4_bi'        , $request->get('valorMetaArrecadacao4Bi'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_5_bi'        , $request->get('valorMetaArrecadacao5Bi'));
            $obTTCMGOMetasArrecadacaoReceita->setDado('meta_arrecadacao_6_bi'        , $request->get('valorMetaArrecadacao6Bi'));

            $obTTCMGOMetasArrecadacaoReceita->recuperaPorChave($rsTTCMGOMetasArrecadacaoReceita,$boTransacao);

            if ($rsTTCMGOMetasArrecadacaoReceita->getNumLinhas() < 0) {
                $obErro = $obTTCMGOMetasArrecadacaoReceita->inclusao($boTransacao);
            } else {
                $obErro = $obTTCMGOMetasArrecadacaoReceita->alteracao($boTransacao);
            }

            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId(),"Configurar Metas de Arrecadação de Receitas concluído com sucesso!","manter","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }

            $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obTTCMGOMetasArrecadacaoReceita);
        }

        break;
}

?>
