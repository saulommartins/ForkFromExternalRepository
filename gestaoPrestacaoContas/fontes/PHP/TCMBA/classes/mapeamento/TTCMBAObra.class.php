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
/*
    * Mapeamento tcmba.obra
    * Data de Criação   : 14/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBAObra.class.php 63771 2015-10-08 13:39:13Z jean $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObra extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.obra');
        $this->setCampoCod('');
        $this->setComplementoChave('cod_obra, cod_tipo, cod_entidade, exercicio');

        $this->AddCampo('cod_obra'              , 'integer' , true  , ''    , true , false);
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''    , true , true );
        $this->AddCampo('exercicio'             , 'varchar' , true  , '4'   , true , true );
        $this->AddCampo('cod_tipo'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('local'                 , 'varchar' , true  , '50'  , false, false);
        $this->AddCampo('cep'                   , 'varchar' , true  , '8'   , false, true );
        $this->AddCampo('cod_bairro'            , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_uf'                , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_municipio'         , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_funcao'            , 'integer' , true  , ''    , false, true );
        $this->AddCampo('nro_obra'              , 'varchar' , true  , '10'  , false, false);
        $this->AddCampo('descricao'             , 'varchar' , true  , '255' , false, false);
        $this->AddCampo('vl_obra'               , 'numeric' , true  , '16,2', false, false);
        $this->AddCampo('data_cadastro'         , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_inicio'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_aceite'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('prazo'                 , 'integer' , true  , ''    , false, false);
        $this->AddCampo('data_recebimento'      , 'date'    , true  , ''    , false, false);
        $this->AddCampo('cod_licitacao'         , 'integer' , false , ''    , false, true );
        $this->AddCampo('cod_modalidade'        , 'integer' , false , ''    , false, true );
        $this->AddCampo('exercicio_licitacao'   , 'varchar' , false , '4'   , false, true );
    }

    public function proximoCod(&$inCod, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql = $this->montaProximoCod().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $inCod = $rsRecordSet->getCampo("cod_obra") + 1;
        }

        return $obErro;
    }
    
    public function montaProximoCod()
    {
        $stSql = "SELECT MAX(cod_obra) AS cod_obra
                    FROM tcmba.obra
                   WHERE obra.cod_tipo=".$this->getDado('cod_tipo')."
                     AND obra.cod_entidade=".$this->getDado('cod_entidade')."
                     AND obra.exercicio='".$this->getDado('exercicio')."'";

        return $stSql;
    }
    
    public function recuperaObra(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;

        $stSql = $this->montaRecuperaObra().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaObra()
    {
        $stSql = " SELECT obra.cod_obra
                        , obra.cod_entidade
                        , obra.exercicio
                        , obra.cod_tipo
                        , tipo_obra.descricao AS nom_tipo
                        , obra.local
                        , obra.cep
                        , obra.cod_bairro
                        , obra.cod_uf
                        , obra.cod_municipio
                        , obra.cod_funcao
                        , obra.nro_obra
                        , obra.descricao
                        , obra.vl_obra
                        , TO_CHAR(obra.data_cadastro,'dd/mm/yyyy') AS data_cadastro
                        , TO_CHAR(obra.data_inicio,'dd/mm/yyyy') AS data_inicio
                        , TO_CHAR(obra.data_aceite,'dd/mm/yyyy') AS data_aceite
                        , obra.prazo
                        , TO_CHAR((obra.data_inicio+obra.prazo),'dd/mm/yyyy') AS data_prazo
                        , TO_CHAR(obra.data_recebimento,'dd/mm/yyyy') AS data_recebimento
                        , obra.cod_licitacao
                        , obra.cod_modalidade
                        , obra.exercicio_licitacao
                        , obra.cod_licitacao||'/'||obra.exercicio_licitacao AS st_licitacao

                     FROM tcmba.obra
               INNER JOIN tcmba.tipo_obra
                       ON tipo_obra.cod_tipo=obra.cod_tipo";

        return $stSql;
    }
}
