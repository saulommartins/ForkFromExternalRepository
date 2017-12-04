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
    * Página de Processamento de Replicar Função
    * Data de Criação: 19/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-01.03.95

    $Id: PRReplicarFuncao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ReplicarFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "replicar":
        Sessao::setTrataExcecao(true);
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php");
        include_once(CAM_GA_ADM_MAPEAMENTO."FAdministracaoReplicaFuncoesExternas.class.php");

        $arFuncao = explode(".",$_POST['inCodFuncao']);

        $obTAdministracaoFuncao = new TAdministracaoFuncao;

        $stFiltro = " AND F.nom_funcao = '".$_POST['stFuncaoCriada']."'";
        $obTAdministracaoFuncao->recuperaRelacionamento( $rsRecordSet, $stFiltro );

        // Verifica se já existe outra função com o mesmo nome.
        if ($rsRecordSet->getNumLinhas() > 0) {
            SistemaLegado::exibeAviso("Já existe uma função com esse nome","n_incluir","erro");
            Sessao::encerraExcecao();
        } else {
            $obFAdministracaoReplicaFuncoesExternas = new FAdministracaoReplicaFuncoesExternas;
            $obFAdministracaoReplicaFuncoesExternas->setDado("cod_modulo_origem",$_POST['inCodModulo']);
            $obFAdministracaoReplicaFuncoesExternas->setDado("cod_biblioteca_origem",$_POST['inCodBiblioteca']);
            $obFAdministracaoReplicaFuncoesExternas->setDado("cod_funcao_origem",$arFuncao[2]);
            $obFAdministracaoReplicaFuncoesExternas->setDado("cod_modulo_destino",$_POST['inCodModuloC']);
            $obFAdministracaoReplicaFuncoesExternas->setDado("cod_biblioteca_destino",$_POST['inCodBibliotecaC']);
            $obFAdministracaoReplicaFuncoesExternas->setDado("nom_funcao",$_POST['stFuncaoCriada']);
            $obFAdministracaoReplicaFuncoesExternas->replicaFuncoesExternas($rsRetorno);

            // Caso ocorra algum problema ao replicar a função.
            if ($rsRetorno->getCampo("retorno") == "f")
                SistemaLegado::exibeAviso("Essa função não pode ser replicada","n_incluir","erro");
            else
                SistemaLegado::alertaAviso($pgForm,"Função replicada com sucesso","incluir","aviso", Sessao::getId(), "../");

            Sessao::encerraExcecao();
        }
    break;
}
?>
