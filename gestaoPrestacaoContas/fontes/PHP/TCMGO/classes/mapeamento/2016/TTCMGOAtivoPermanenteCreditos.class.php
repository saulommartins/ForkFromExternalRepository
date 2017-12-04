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
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAtivoPermanenteCreditos.class.php 65168 2016-04-29 16:36:09Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php";

class TTCMGOAtivoPermanenteCreditos  extends TContabilidadeBalancoFinanceiro
{
    public function __construct()
    {
        parent::TContabilidadeBalancoFinanceiro();
    }

    function recuperaRegistro10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaRegistro10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaRegistro10()
    {
        $stSql = " SELECT DISTINCT 
                         0 AS numero_registro
                       , 10 AS tipo_registro
                       , 0.00 AS vl_cancelamento
                       , 0.00 AS vl_encampacao
                       , *
                       , '".$this->getDado( 'stExercicio' )."' AS exercicio
                    FROM tcmgo.ativo_permanente_creditos ( '".$this->getDado( 'stExercicio' )."'
                                                          , ' cod_estrutural ilike ''1.2%'' '
                                                          ,'".$this->getDado( 'stDataInicio' )."'
                                                          ,'".$this->getDado( 'stDataFim' )."'
                                                          ,'".$this->getDado ( 'stEntidades' )."'
                                                       )
                         AS retorno (  cod_estrutural    VARCHAR
                                     , nivel             INTEGER
                                     , nom_conta         VARCHAR
                                     , num_orgao         INTEGER
                                     , cod_unidade       INTEGER
                                     , vl_saldo_anterior NUMERIC
                                     , vl_saldo_debitos  NUMERIC
                                     , vl_saldo_creditos NUMERIC
                                     , vl_saldo_atual    NUMERIC
                                     , nom_sistema       VARCHAR
                                     , tipo_lancamento   INTEGER
                                    )
                    WHERE vl_saldo_anterior <> 0
                       OR vl_saldo_debitos  <> 0
                       OR vl_saldo_creditos <> 0
                    
                 ORDER BY cod_estrutural ";
        return $stSql;
    }

}

?>