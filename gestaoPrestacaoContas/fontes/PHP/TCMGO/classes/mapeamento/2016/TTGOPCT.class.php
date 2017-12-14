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
    * Classe de mapeamento para o arquivo de exportação PCT
    * Data de Criação: 19/02/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOPCT.class.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGOPCT extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaRegistro10(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRegistro10();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRegistro10()
    {
        $stSql = "
            SELECT 10 AS tipo_registro
                 , cod_tipo
                 , 0 AS planocontas
              FROM tcmgo.orgao
             WHERE exercicio = '".Sessao::getExercicio()."'
            ";

        return $stSql;
    }

    public function recuperaRegistros(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRegistros($stFiltro);
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRegistros()
    {
        $stSql = "SELECT
                            tipo_registro
                            , tipo_unidade_orcamentaria
                            , REPLACE(cod_estrutural, '.', '') AS cod_conta
                            , indicador_superavit
                            , nivel
                            , CASE WHEN natureza_conta = 'M' THEN
                                'X'
                              ELSE
                                natureza_conta
                              END AS natureza_conta
                            , tipo_conta
                            , descricao
                            , indicador_superavit_pcasp
                            , cod_conta_casp
                            , cod_conta_pai
                            , cod_conta_pcasp
                    FROM
                         fn_relatorio_pct( '".$this->getDado('exercicio')."'
                                           , '".$this->getDado('entidades')."'
                                           , '".$this->getDado('dt_inicial')."'
                                           , '".$this->getDado('dt_final')."') AS retorno ( tipo_registro                  INTEGER
                                                                                            , tipo_unidade_orcamentaria    INTEGER
                                                                                            , cod_conta                    INTEGER
                                                                                            , indicador_superavit          INTEGER
                                                                                            , nivel		                     INTEGER
                                                                                            , natureza_conta               CHAR
                                                                                            , tipo_conta                   VARCHAR
                                                                                            , descricao                    VARCHAR
                                                                                            , indicador_superavit_pcasp    INTEGER
                                                                                            , cod_conta_casp               VARCHAR
                                                                                            , cod_conta_pai                VARCHAR
                                                                                            , cod_conta_pcasp              VARCHAR
                                                                                            , vl_saldo_anterior	           NUMERIC
                                                                                            , vl_saldo_debitos	           NUMERIC
                                                                                            , vl_saldo_creditos	           NUMERIC
                                                                                            , vl_saldo_atual               NUMERIC
                                                                                            , cod_estrutural               VARCHAR
                                                                                            , obrigatorio_tcmgo            BOOLEAN
                                                                                        ) ";

        return $stSql;
    }
}
?>
