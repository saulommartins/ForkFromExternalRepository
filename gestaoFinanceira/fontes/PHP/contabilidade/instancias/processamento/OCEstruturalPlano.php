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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

* $Id: OCEstruturalPlano.php 63906 2015-11-05 12:31:01Z franver $

Casos de uso: uc-02.02.02
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaAnalitica.class.php';
require_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadePlanoConta.class.php';

function buscaPopup()
{
    $stErro = '';
    
    if( $_REQUEST['stEscrituracao'] == 'sintetica' ) {
        $rsConta = new RecordSet();
        $stCondicao = " AND plano_conta.escrituracao = '".$_REQUEST['stEscrituracao']."' \n";
        
        $obTContabilidadePlanoConta = new TContabilidadePlanoConta();
        $obTContabilidadePlanoConta->setDado('exercicio',Sessao::getExercicio());
        $obTContabilidadePlanoConta->setDado('cod_estrutural',$_REQUEST['stCodEstrutural']);
        $obTContabilidadePlanoConta->recuperaContaSintetica($rsConta, $stCondicao, $stOrdem, $boTransacao);

        $stDescricao = $rsConta->getCampo('nom_conta');
        
        if(trim($stDescricao) == '') {
            $stErro = "Conta (".$_REQUEST['stCodEstrutural'].") não foi encontrada, ou não é do tipo Sintética.";
        }

    } else {
        if ($_GET[$_GET['stNomCampoCod']]) {
        
            $stExercicio = isset($_REQUEST['stExercicio']) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();
        
            $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
            $obRContabilidadePlanoContaAnalitica->setCodEstrutural( $_GET[$_GET['stNomCampoCod']] );
            $obRContabilidadePlanoContaAnalitica->setExercicio( $stExercicio );
            $obErro = $obRContabilidadePlanoContaAnalitica->consultar();
            
            $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
            
            if(trim($stDescricao) == '') {
                $stErro = "Conta (".$_REQUEST['stCodEstrutural'].") não foi encontrada.";
            }
        }
    }
    
    if(trim($stErro) != '') {
        $stJs .= " alertaAviso('".$stErro."','form','aviso','".Sessao::getId()."'); \n";
    }
    $stJs .= "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
    
    
    return $stJs;
}
switch ($_GET['stCtrl']) {
    case 'buscaPopup':
        $stJs .= buscaPopup();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>
