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
  * Classe de mapeamento da tabela PESSOAL.SUB_DIVISAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandre Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Id: TPessoalSubDivisao.class.php 63224 2015-08-05 16:56:50Z evandro $

    Caso de uso: uc-04.04.06
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  PESSOAL.SUB_DIVISAO
  * Data de Criação: 07/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandre Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalSubDivisao extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalSubDivisao()
    {
        parent::Persistente();
        $this->setTabela('pessoal.sub_divisao');

        $this->setCampoCod('cod_sub_divisao');
        $this->setComplementoChave('');

        $this->AddCampo('cod_sub_divisao','INTEGER',true,'',true,false);
        $this->AddCampo('cod_regime','INTEGER',true,'',false,true);
        $this->AddCampo('descricao','varchar',true,'80',false,false);

    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT                                       \n";
        $stSQL .= "     pr.descricao as nom_regime,              \n";
        $stSQL .= "     pr.cod_regime,                           \n";
        $stSQL .= "     psd.descricao as nom_sub_divisao,        \n";
        $stSQL .= "     psd.cod_sub_divisao,                     \n";
        $stSQL .= "     psd.cod_regime                           \n";
        $stSQL .= " FROM                                         \n";
        $stSQL .= "    pessoal".Sessao::getEntidade().".regime as pr,          \n";
        $stSQL .= "    pessoal".Sessao::getEntidade().".sub_divisao as psd     \n";
        $stSQL .= " WHERE                                        \n";
        $stSQL .= "     pr.cod_regime = psd.cod_regime           \n";

        return $stSQL;
    }

    public function validaExclusao($stFiltro = "", $boTransacao = "")
    {
        $obErro = new erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaValidaExclusao().$stFiltro;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                $obErro->setDescricao('Esta subdivisão está sendo utilizada por um cargo, por esse motivo não pode ser excluído!');
            }
        }

        return $obErro;
    }

    public function montaValidaExclusao()
    {
        $stSQL  =" SELECT pr.descricao  as nom_regime                                \n";
        $stSQL .="      , pr.cod_regime                                              \n";
        $stSQL .="      , psd.descricao as nom_sub_divisao                           \n";
        $stSQL .="      , psd.cod_sub_divisao                                        \n";
        $stSQL .="      , psd.cod_regime                                             \n";
        $stSQL .=" FROM pessoal".Sessao::getEntidade().".regime      as pr                         \n";
        $stSQL .="    , pessoal".Sessao::getEntidade().".sub_divisao as psd                        \n";
        $stSQL .="    , folhapagamento.configuracao_evento_caso_sub_divisao as fcecs \n";
        $stSQL .=" WHERE pr.cod_regime       = psd.cod_regime                        \n";
        $stSQL .="   AND psd.cod_sub_divisao = fcecs.cod_sub_divisao                 \n";
        $stSQL .="   AND psd.cod_sub_divisao = ".$this->getDado('cod_sub_divisao')." \n";

        return $stSQL;
    }

    public function recuperaDeParaTipoCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDeParaTipoCargo().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDeParaTipoCargo()
    {
        $stSql  = "    SELECT sub_divisao.cod_sub_divisao                                       \n";
        $stSql .= "         , sub_divisao.cod_regime                                            \n";
        $stSql .= "         , sub_divisao.descricao                                             \n";
        $stSql .= "         , de_para_tipo_cargo.cod_tipo_cargo_tce                             \n";
        $stSql .= "         , regime.descricao AS descricao_regime                              \n";
        $stSql .= "      FROM pessoal".Sessao::getEntidade().".sub_divisao                      \n";
        $stSql .= "      JOIN pessoal".Sessao::getEntidade().".regime                           \n";
        $stSql .= "        ON regime.cod_regime = sub_divisao.cod_regime                        \n";
        $stSql .= " LEFT JOIN pessoal".Sessao::getEntidade().".de_para_tipo_cargo               \n";
        $stSql .= "        ON de_para_tipo_cargo.cod_sub_divisao = sub_divisao.cod_sub_divisao  \n";

        return $stSql;
    }
    
    public function recuperaDeParaTipoCargoTCMBA(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDeParaTipoCargoTCMBA().$stFiltro.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDeParaTipoCargoTCMBA()
    {
        $stSql  = " SELECT sub_divisao.cod_sub_divisao                                       
                         , sub_divisao.cod_regime                                            
                         , sub_divisao.descricao                                             
                         , de_para_tipo_cargo_tcmba.cod_tipo_regime_tce
                         , de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce
                         , regime.descricao AS descricao_regime                              
                    FROM pessoal".Sessao::getEntidade().".sub_divisao                      
                    
                    JOIN pessoal".Sessao::getEntidade().".regime                           
                      ON regime.cod_regime = sub_divisao.cod_regime                        
                    
                    LEFT JOIN pessoal".Sessao::getEntidade().".de_para_tipo_cargo_tcmba         
                           ON de_para_tipo_cargo_tcmba.cod_sub_divisao = sub_divisao.cod_sub_divisao  
                ";
        return $stSql;
    }

    public function recuperaDeParaTipoRegimeTrabalho(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
    {
        $stSql  = "\n    SELECT sub_divisao.cod_sub_divisao";
        $stSql .= "\n         , sub_divisao.cod_regime";
        $stSql .= "\n         , sub_divisao.descricao";
        $stSql .= "\n         , de_para_tipo_regime_trabalho.cod_tipo_regime_trabalho_tce";
        $stSql .= "\n         , regime.descricao AS descricao_regime";
        $stSql .= "\n      FROM pessoal".Sessao::getEntidade().".sub_divisao";
        $stSql .= "\n      JOIN pessoal".Sessao::getEntidade().".regime";
        $stSql .= "\n        ON regime.cod_regime = sub_divisao.cod_regime";
        $stSql .= "\n LEFT JOIN pessoal".Sessao::getEntidade().".de_para_tipo_regime_trabalho";
        $stSql .= "\n        ON de_para_tipo_regime_trabalho.cod_sub_divisao = sub_divisao.cod_sub_divisao";

        $this->executaRecuperaSql($stSql, $rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
    }

}
