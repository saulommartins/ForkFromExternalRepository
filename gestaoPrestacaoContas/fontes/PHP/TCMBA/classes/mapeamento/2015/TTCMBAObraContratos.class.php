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
    * Mapeamento tcmba.obra_contratos
    * Data de Criação   : 14/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBAObraContratos.class.php 63632 2015-09-22 17:42:03Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObraContratos extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.obra_contratos');
        $this->setComplementoChave('cod_obra, cod_tipo, cod_entidade, exercicio, cod_contratacao, numcgm');

        $this->AddCampo('cod_obra'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''    , true , true );
        $this->AddCampo('exercicio'             , 'varchar' , true  , '4'   , true , true );
        $this->AddCampo('cod_tipo'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_contratacao'       , 'integer' , true  , ''    , true , true );
        $this->AddCampo('nro_instrumento'       , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_contrato'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_convenio'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_parceria'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('numcgm'                , 'integer' , true  , ''    , true , true );
        $this->AddCampo('funcao_cgm'            , 'varchar' , true  , '50'  , false, false);
        $this->AddCampo('data_inicio'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_final'            , 'date'    , true  , ''    , false, false);
        $this->AddCampo('lotacao'               , 'varchar' , false , '50'  , false, false);
    }

    public function recuperaObraContrato(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaObraContrato().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaObraContrato()
    {
        $stSql ="   SELECT  1 AS tipo_registro
                            , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                            ,obra.nro_obra
                            ,obra_contratos.nro_contrato
                            ,'".$this->getDado('competencia')."' as competencia
                 
                       FROM tcmba.obra_contratos
                 
                 INNER JOIN tcmba.obra
                         ON obra.cod_obra     = obra_contratos.cod_obra
                        AND obra.cod_entidade = obra_contratos.cod_entidade
                        AND obra.exercicio    = obra_contratos.exercicio
                        AND obra.cod_tipo     = obra_contratos.cod_tipo

                      WHERE obra_contratos.cod_entidade IN (".$this->getDado('entidades').")  
                        AND obra_contratos.data_inicio <= TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                        AND obra_contratos.data_final >= TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                        AND obra_contratos.nro_contrato <> ''
        ";
        
        return $stSql;
    }
}
