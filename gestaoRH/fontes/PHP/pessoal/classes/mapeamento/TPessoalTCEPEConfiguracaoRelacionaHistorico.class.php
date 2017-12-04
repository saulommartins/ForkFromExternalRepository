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
     * 
    * Data de Criação   : 26/09/2014

    * @author Analista:
    * @author Desenvolvedor:  Lisiane Morais
    * @ignore

    $Id: TPessoalTCEPEConfiguracaoRelacionaHistorico.class.php 60247 2014-10-08 17:06:26Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalTCEPEConfiguracaoRelacionaHistorico extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    
    function TPessoalTCEPEConfiguracaoRelacionaHistorico()
    {
        parent::Persistente();
        $this->setTabela('pessoal'.Sessao::getEntidade().'.tcepe_configuracao_relaciona_historico');
        
        $this->setCampoCod('cod_assentamento');
        $this->setComplementoChave('cod_tipo_movimentacao, exercicio');
        
        $this->AddCampo('cod_assentamento'     ,'integer' ,true ,''    ,true ,false);
        $this->AddCampo('cod_tipo_movimentacao','integer' ,true ,''    ,true ,true);
        $this->AddCampo('exercicio'            ,'varchar' ,true ,''    ,true ,false);
        $this->AddCampo('timestamp'            ,'timestamp',true,''    ,true ,true);
    }
    
    function montaRecuperaRelacionamento()
    {
        $stSql = "SELECT *
                    FROM pessoal".Sessao::getEntidade().".tcepe_configuracao_relaciona_historico
                ";
                
        return $stSql;
    }
    
}