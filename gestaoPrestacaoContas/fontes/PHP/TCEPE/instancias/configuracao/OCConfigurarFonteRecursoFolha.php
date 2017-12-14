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

/**
  * Pacote de configuração do TCEPE
  * Data de Criação: 01/10/2014
  * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
  *
  $Id: $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

# Mapeamentos TCEPE
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFonteRecursoLotacao.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFonteRecursoLocal.class.php";

# Componentes RH
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php";
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php";

$stPrograma = "ConfigurarFonteRecursoFolha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');
$stAcao = $request->get('stAcao');

$inCodEntidade = $request->get('inCodEntidade');
$inCodFonte    = $request->get('inCodFonte');
$stExercicio   = Sessao::getExercicio();

function carregaForm($inCodEntidade, $inCodFonte, $stExercicio) {

    # Componente de Lotação
    $obISelectMultiploLotacao = new ISelectMultiploLotacao;
    $obISelectMultiploLotacao->setNull(false);

    # Componente de Local
    $obISelectMultiploLocal = new ISelectMultiploLocal;

    # Novos RecordSet
    $rsLotacoesDisponiveis = $rsLotacoesSelecionados = new RecordSet;
    $rsLocalDisponiveis    = $rsLocalOcupadas    = new RecordSet;

    if (!empty($inCodFonte)) {
        $obTTCEPEFonteRecursoLotacao = new TTCEPEFonteRecursoLotacao;
        $obTTCEPEFonteRecursoLotacao->setDado('cod_fonte'    , $inCodFonte);
        $obTTCEPEFonteRecursoLotacao->setDado('exercicio'    , $stExercicio);
        $obTTCEPEFonteRecursoLotacao->setDado('cod_entidade' , $inCodEntidade);
    
        $obTTCEPEFonteRecursoLotacao->recuperaLotacoesDisponiveis($rsLotacoesDisponiveis);
        $obTTCEPEFonteRecursoLotacao->recuperaLotacoesSelecionados($rsLotacoesSelecionados);
    
        # Preenche a Lotação conforme os registros na base de dados
        $obISelectMultiploLotacao->setDisponiveis($rsLotacoesDisponiveis);
        $obISelectMultiploLotacao->setSelecionados($rsLotacoesSelecionados);

        $obTTCEPEFonteRecursoLocal = new TTCEPEFonteRecursoLocal;
        $obTTCEPEFonteRecursoLocal->setDado('cod_fonte'    , $inCodFonte);
        $obTTCEPEFonteRecursoLocal->setDado('exercicio'    , $stExercicio);
        $obTTCEPEFonteRecursoLocal->setDado('cod_entidade' , $inCodEntidade);
    
        $obTTCEPEFonteRecursoLocal->recuperaLocalDisponiveis($rsLocalDisponiveis);
        $obTTCEPEFonteRecursoLocal->recuperaLocalSelecionados($rsLocalSelecionados);

        # Preenche o Local conforme os registros na base de dados
        $obISelectMultiploLocal->setRecord1($rsLocalDisponiveis);
        $obISelectMultiploLocal->setRecord2($rsLocalSelecionados);
    }

    # Cria novo formulário com os componentes de Lotação e Local.
    $obFormulario = new Formulario;
    $obFormulario->addComponente  ( $obISelectMultiploLotacao );
    $obFormulario->addComponente  ( $obISelectMultiploLocal   );
    $obFormulario->montaInnerHTML();
    $obFormulario->obJavaScript->montaJavaScript();
    $stHTML = $obFormulario->getHTML();

    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    # Guarda em um Hidden os comandos JS para serem executados no submit no formulário principal (FM)
    $stJs  = " jQuery('#hdnJs').val('".$stEval."'); ";
    $stJs .= " jQuery('#spnLotacaoLocal').html('".$stHTML."'); ";

    return $stJs;
}

switch ($stCtrl) {

    case "carregaForm":

        $stJs = carregaForm($inCodEntidade, $inCodFonte, $stExercicio);
    
    break;

}

if (!empty($stJs)) {
    echo $stJs;
}
