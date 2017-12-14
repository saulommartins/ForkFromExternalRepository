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

/*
    * Classe de mapeamento da tabela tcmgo.unidade_responsavel
    * Data de Criação   : 23/12/2008

    * @author Analista      Gelson
    * @author Desenvolvedor Carlos Adriano

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOUnidadeResponsavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TTCMGOUnidadeResponsavel()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.unidade_responsavel");

        $this->setCampoCod('num_unidade');
        $this->setComplementoChave('num_orgao, exercicio');

        $this->AddCampo( 'num_unidade'       , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'num_orgao'         , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'exercicio'         , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'timestamp'         , 'timestamp'  , true  , ''   , true  , true);
        $this->AddCampo( 'cgm_gestor'        , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'gestor_dt_inicio'  , 'date'       , true  , ''   , true  , true);
        $this->AddCampo( 'gestor_dt_fim'     , 'date'       , false  , ''   , true  , true);
        $this->AddCampo( 'tipo_responsavel'  , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'gestor_cargo'      , 'char'       , false  , '50' , true  , true);
        $this->AddCampo( 'cgm_contador'      , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'contador_dt_inicio', 'date'       , true  , ''   , true  , true);
        $this->AddCampo( 'contador_dt_fim'   , 'date'       , false  , ''   , true  , true);
        $this->AddCampo( 'contador_crc'      , 'char'       , false  , '11' , true  , true);
        $this->AddCampo( 'uf_crc'            , 'integer'    , false  , ''   , true  , true);
        $this->AddCampo( 'cod_provimento_contabil'   , 'integer'    , false  , ''   , true  , true);
        $this->AddCampo( 'cgm_controle_interno'      , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'controle_interno_dt_inicio', 'date'       , true  , ''   , true  , true);
        $this->AddCampo( 'controle_interno_dt_fim'   , 'date'       , false  , ''   , true  , true);
        $this->AddCampo( 'cgm_juridico'              , 'integer'    , true  , ''   , true  , true);
        $this->AddCampo( 'juridico_dt_inicio'        , 'date'       , true  , ''   , true  , true);
        $this->AddCampo( 'juridico_dt_fim'           , 'date'       , false  , ''   , true  , true);
        $this->AddCampo( 'juridico_oab'              , 'char'       , false  , '8' , true  , true);
        $this->AddCampo( 'uf_oab'                    , 'integer'    , false  , ''   , true  , true);
        $this->AddCampo( 'cod_provimento_juridico'   , 'integer'    , false  , ''   , true  , true);
    }

    public function recuperaPorUnidade(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaPorUnidade();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

        return $obErro;
    }

    public function montaRecuperaPorUnidade()
    {
        $stSql  = "    SELECT *                                                                      \n";
        $stSql .= "            , unidade_responsavel.num_unidade                                     \n";
        $stSql .= "            , unidade_responsavel.num_orgao                                       \n";
        $stSql .= "            , unidade_responsavel.exercicio                                       \n";
        $stSql .= "            , unidade_responsavel.timestamp                                       \n";
        $stSql .= "            , cgmgestor.nom_cgm as nom_gestor                                     \n";
        $stSql .= "            , cgmcontador.nom_cgm as nom_contador                                 \n";
        $stSql .= "            , cgmjuridico.nom_cgm as nom_juridico                                 \n";
        $stSql .= "            , cgmcontroleinterno.nom_cgm as nom_controle_interno                  \n";
        $stSql .= "      FROM tcmgo.unidade_responsavel                                              \n";

        $stSql .= "INNER JOIN sw_cgm cgmgestor                                                       \n";
        $stSql .= "        ON cgmgestor.numcgm     = unidade_responsavel.cgm_gestor                  \n";

        $stSql .= "INNER JOIN sw_cgm cgmcontador                                                     \n";
        $stSql .= "        ON cgmcontador.numcgm   = unidade_responsavel.cgm_contador                \n";

        $stSql .= "INNER JOIN sw_cgm cgmjuridico                                                     \n";
        $stSql .= "        ON cgmjuridico.numcgm   = unidade_responsavel.cgm_juridico                \n";

        $stSql .= "INNER JOIN sw_cgm cgmcontroleinterno                                              \n";
        $stSql .= "        ON cgmcontroleinterno.numcgm   = unidade_responsavel.cgm_controle_interno \n";

        $stSql .= " LEFT JOIN tcmgo.contador_terceirizado                                            \n";
        $stSql .= "        ON contador_terceirizado.exercicio   = unidade_responsavel.exercicio      \n";
        $stSql .= "       AND contador_terceirizado.num_orgao   = unidade_responsavel.num_orgao      \n";
        $stSql .= "       AND contador_terceirizado.num_unidade = unidade_responsavel.num_unidade    \n";
        $stSql .= "       AND contador_terceirizado.timestamp   = unidade_responsavel.timestamp      \n";

        $stSql .= " LEFT JOIN tcmgo.juridico_terceirizado                                            \n";
        $stSql .= "        ON juridico_terceirizado.exercicio   = unidade_responsavel.exercicio      \n";
        $stSql .= "       AND juridico_terceirizado.num_orgao   = unidade_responsavel.num_orgao      \n";
        $stSql .= "       AND juridico_terceirizado.num_unidade = unidade_responsavel.num_unidade    \n";
        $stSql .= "       AND juridico_terceirizado.timestamp   = unidade_responsavel.timestamp      \n";

        $stSql .= "     WHERE unidade_responsavel.num_orgao   = ".$this->getDado('num_orgao')."      \n";
        $stSql .= "       AND unidade_responsavel.num_unidade = ".$this->getDado('num_unidade')."    \n";
        $stSql .= "       AND unidade_responsavel.exercicio   = '".Sessao::getExercicio()."'         \n";

        $stSql .= "  ORDER BY unidade_responsavel.timestamp DESC LIMIT 1                             \n";

        return $stSql;
    }

}
