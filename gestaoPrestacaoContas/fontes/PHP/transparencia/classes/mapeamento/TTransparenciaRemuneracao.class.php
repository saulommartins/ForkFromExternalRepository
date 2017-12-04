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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 16/11/2012

    * @author Analista: Gelson
    * @author Desenvolvedor: Carlos

    * @package URBEM
    * @subpackage Mapeamento

    $Revision:
    $Name$
    $Author:
    $Date:

    * Casos de uso:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTransparenciaRemuneracao extends Persistente
{
     function recuperaUltimoTimesTampPeriodoMovimentacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
     {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaUltimoTimesTampPeriodoMovimentacao().$stOrdem;
      $this->setDebug( $stSql );
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
     }

     function montaRecuperaUltimoTimesTampPeriodoMovimentacao()
     {
      $stSql = " select ultimotimestampperiodomovimentacao(".$this->getDado('inCodPeriodoMovimentacao') .",'".$this->getDado('stEntidade') ."') ";

      return $stSql;
     }

     function recuperaRemuneracao(&$rsRecordSet, $stFiltro = "" , $stOrdem = "" , $boTransacao = "")
     {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaRemuneracao().$stFiltro.$stOrdem;
      $this->setDebug( $stSql );
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

      return $obErro;
     }

     function montaRecuperaRemuneracao()
     {
      $stSql = " SELECT *
             , '".$this->getDado('dt_final')."'::VARCHAR AS mesano
               FROM fn_transparencia_remuneracao('".$this->getDado('st_entidade')."', ".$this->getDado('cod_periodo_movimentacao').", '".$this->getDado('exercicio')."', ".$this->getDado('cod_entidade').")
             AS tabela(
                 exercicio                 CHAR(4),
                 cod_entidade 	           INTEGER,
                 cod_periodo_movimentacao  INTEGER,
                 registro     	           INTEGER,
                 cod_contrato	           INTEGER,
                 cgm          	           VARCHAR,
                 remuneracao_bruta         VARCHAR,
                 redutor_teto              VARCHAR,
                 remuneracao_natalina      VARCHAR,
                 remuneracao_ferias        VARCHAR,
                 remuneracao_outras        VARCHAR,
                 deducoes_irrf             VARCHAR,
                 deducoes_obrigatorias     VARCHAR,
                 demais_deducoes           VARCHAR,
                 salario_familia           VARCHAR,
                 jetons        	           VARCHAR,
                 verbas        	           VARCHAR,
                 remuneracao_apos_deducoes NUMERIC
             )  WHERE remuneracao_bruta     != '' OR
                  redutor_teto          != '' OR
                  remuneracao_natalina  != '' OR
                  remuneracao_ferias    != '' OR
                  remuneracao_outras    != '' OR
                  deducoes_irrf         != '' OR
                  deducoes_obrigatorias != '' OR
                  demais_deducoes       != '' OR
                  salario_familia       != '' OR
                  jetons        	    != '' OR
                  verbas        	    != '' ";

      return $stSql;
     }

     function limpaTabelaTemporaria()
     {
          $obErro      = new Erro;
          $obConexao   = new Conexao;
          $rsRecordSet = new RecordSet;
          $stSql = $this->montaLimpaTabelaTemporaria();
          $this->setDebug( $stSql );
          $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

          return $obErro;
     }

     function montaLimpaTabelaTemporaria()
     {
          $stSql = 'DELETE FROM temp_transparencia_remuneracao';
     }
}

?>
