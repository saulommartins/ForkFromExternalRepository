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
    * Classe de mapeamento da tabela tcepe.plano_banco_tipo_conta_banco
    * 
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEPlanoBancoTipoConta.class.php 60168 2014-10-03 15:18:13Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEPlanoBancoTipoConta extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEPlanoBancoTipoConta()
    {
        parent::Persistente();
        $this->setTabela('tcepe.plano_banco_tipo_conta_banco');

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio'             , 'char'    , true, '4' , true, true);
        $this->AddCampo('cod_plano'             , 'integer' , true, ''  , true, true);
        $this->AddCampo('cod_tipo_conta_banco'  , 'integer' , true, ''  , true, true);
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaPlanoBancoTipoConta.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaPlanoBancoTipoConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPlanoBancoTipoConta().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPlanoBancoTipoConta()
    {
        $stSql  = " SELECT PBT.*                                                    \n";
        $stSql .= "      , TRIM(TCB.descricao) AS descricao                         \n";
        $stSql .= "   FROM tcepe.plano_banco_tipo_conta_banco AS PBT                \n";
        $stSql .= "   JOIN tcepe.tipo_conta_banco AS TCB                            \n";
        $stSql .= "     ON TCB.cod_tipo_conta_banco=PBT.cod_tipo_conta_banco        \n";
        
        if (trim($this->getDado('cod_plano'))) {
            $stSql .= "  WHERE PBT.cod_plano=".trim($this->getDado('cod_plano'))."  \n";
            $stSql .= "    AND PBT.exercicio='".Sessao::getExercicio()."'           \n";    
        }
    
        return $stSql;
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaListaTipoContaBanco.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function listaTipoContaBanco(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaListaTipoContaBanco().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaTipoContaBanco()
    {
        $stSql = " SELECT tipo_conta_banco.cod_tipo_conta_banco
                        , TRIM(tipo_conta_banco.descricao) AS descricao
                     FROM tcepe.tipo_conta_banco ";
    
        return $stSql;
    }

}
