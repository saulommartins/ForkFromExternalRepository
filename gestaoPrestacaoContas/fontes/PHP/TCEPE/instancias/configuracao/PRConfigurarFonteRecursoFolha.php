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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    *
    $Id: PRConfigurarFonteRecursoFolha.php 60373 2014-10-16 14:35:21Z diogo.zarpelon $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFonteRecursoLotacao.class.php";
include_once CAM_GPC_TCEPE_MAPEAMENTO."TTCEPEFonteRecursoLocal.class.php";

$stPrograma = "ConfigurarFonteRecursoFolha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTTCEPEFonteRecursoLotacao = new TTCEPEFonteRecursoLotacao();
$obTTCEPEFonteRecursoLocal   = new TTCEPEFonteRecursoLocal();

# Inicia o controle de Transação
Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEPEFonteRecursoLotacao );

$stAcao = $request->get('stAcao');

$obErro = new Erro();

switch ($stAcao) {

    case 'configurar':
        $inCodEntidade = $request->get('inCodEntidade');
        $inCodFonte    = $request->get('inCodFonte');
        $stExercicio   = Sessao::getExercicio();
        $arCodLotacao  = $request->get('inCodLotacaoSelecionados');
        $arCodLocal    = $request->get('inCodLocalSelecionados');

        # Seleção de Lotações é obrigatório 
        if (!is_array($arCodLotacao) || count($arCodLotacao) < 0) {
            $obErro->setDescricao("Informe ao menos uma lotação para o Recurso.");
        }

        if (!$obErro->ocorreu()) {
            $obTTCEPEFonteRecursoLotacao->setDado( 'cod_fonte'    , $inCodFonte    );
            $obTTCEPEFonteRecursoLotacao->setDado( 'exercicio'    , $stExercicio   );
            $obTTCEPEFonteRecursoLotacao->setDado( 'cod_entidade' , $inCodEntidade );
            $obErro = $obTTCEPEFonteRecursoLotacao->exclusao( $boTransacao );
        }
        
        if (!$obErro->ocorreu()) {
            $obTTCEPEFonteRecursoLocal->setDado( 'cod_fonte'    , $inCodFonte    );
            $obTTCEPEFonteRecursoLocal->setDado( 'exercicio'    , $stExercicio   );
            $obTTCEPEFonteRecursoLocal->setDado( 'cod_entidade' , $inCodEntidade );
            $obErro = $obTTCEPEFonteRecursoLocal->exclusao( $boTransacao );
        }
        
        # Adiciona Lotações
        foreach ($arCodLotacao as $inCodLotacao) {
            if ( !$obErro->ocorreu() ) {
                $obTTCEPEFonteRecursoLotacao->setDado( 'cod_fonte'    , $inCodFonte    );
                $obTTCEPEFonteRecursoLotacao->setDado( 'exercicio'    , $stExercicio   );
                $obTTCEPEFonteRecursoLotacao->setDado( 'cod_entidade' , $inCodEntidade );
                $obTTCEPEFonteRecursoLotacao->setDado( 'cod_orgao'    , $inCodLotacao  );
                $obErro = $obTTCEPEFonteRecursoLotacao->inclusao( $boTransacao );
            }
        }

        # Adiciona Locais
        if (is_array($arCodLocal) && count($arCodLocal) > 0) {
            foreach ($arCodLocal as $inCodLocal) {
                if ( !$obErro->ocorreu() ) {
                    $obTTCEPEFonteRecursoLocal->setDado( 'cod_fonte'    , $inCodFonte    );
                    $obTTCEPEFonteRecursoLocal->setDado( 'exercicio'    , $stExercicio   );
                    $obTTCEPEFonteRecursoLocal->setDado( 'cod_entidade' , $inCodEntidade );
                    $obTTCEPEFonteRecursoLocal->setDado( 'cod_local'    , $inCodLocal    );
                    $obErro = $obTTCEPEFonteRecursoLocal->inclusao( $boTransacao );
                }
            }
        }

        Sessao::encerraExcecao();
        
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"","incluir","incluir_n", Sessao::getId(), "../");
        }else{
            SistemaLegado::exibeAviso($obErro->getDescricao(),"","n_incluir","erro");
        }

    break;
}

?>