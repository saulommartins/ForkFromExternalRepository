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
  * Página de Processamento de Configuração de Receita/Despesa Extra por Fonte de Recurso
  * Data de Criação: 05/11/2015

  * @author Analista: Valtair Santos
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: PRManterReceitaDespesaExtraRecurso.php 63906 2015-11-05 12:31:01Z franver $
  * $Revision: 63906 $
  * $Author: franver $
  * $Date: 2015-11-05 10:31:01 -0200 (Thu, 05 Nov 2015) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
require_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeConfiguracaoContasExtras.class.php';
//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitaDespesaExtraRecurso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obTContabilidadeConfiguracaoContasExtras = new TContabilidadeConfiguracaoContasExtras();

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio' , Sessao::getExercicio() );
$obTAdministracaoConfiguracao->setDado('cod_modulo', 9);
$obTAdministracaoConfiguracao->setDado('parametro' , 'indicador_contas_extras_recurso');
$obTAdministracaoConfiguracao->setDado('valor'     , $request->get('boIndicadorSaldoContasRecurso'));
$obErro = $obTAdministracaoConfiguracao->alteracao($boTransacao);

if($request->get('boIndicadorSaldoContasRecurso') == 't' && !$obErro->ocorreu()) {
    $arContas = Sessao::read('arContas');

    if(!is_array($arContas)){
        $arContas = array();
    }
    
    if(count($arContas) <= 0){
        $obErro->setDescricao("É necessário ao menos uma conta ser configurada.");
    }
    
    if( !$obErro->ocorreu() ) {
        $obTContabilidadeConfiguracaoContasExtras->setDado('exercicio', Sessao::getExercicio());
        $obErro = $obTContabilidadeConfiguracaoContasExtras->exclusao($boTransacao);
    }
    
    if(!$obErro->ocorreu()){
        foreach($arContas AS $arConta){
            $obTContabilidadeConfiguracaoContasExtras->setDado('exercicio', $arConta['exercicio']);
            $obTContabilidadeConfiguracaoContasExtras->setDado('cod_conta', $arConta['cod_conta']);
            $obErro = $obTContabilidadeConfiguracaoContasExtras->inclusao($boTransacao);
        }
    }
}

if($request->get('boIndicadorSaldoContasRecurso') == 'f' && !$obErro->ocorreu()){
    $obTContabilidadeConfiguracaoContasExtras->setDado('exercicio', Sessao::getExercicio());
    $obErro = $obTContabilidadeConfiguracaoContasExtras->exclusao($boTransacao);

}
if(!$obErro->ocorreu()){
    SistemaLegado::alertaAviso($pgForm."?stAcao=".$request->get('stAcao'), "Alteração feita com sucesso".$stNroProcesso."! ", 'incluir', "aviso", Sessao::getId(), "../");
    Sessao::remove('arContas');
    $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBATermoParceria);

} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    SistemaLegado::LiberaFrames(true,true);
}

?>