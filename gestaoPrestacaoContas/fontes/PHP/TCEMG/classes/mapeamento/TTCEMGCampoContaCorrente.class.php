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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CLA_PERSISTENTE);

class TTCEMGCampoContaCorrente extends Persistente {
  public function TTCEMGCampoContaCorrente() {
    parent::Persistente();
    $this->setTabela("tcemg.configuracao_dcasp_registros");

    $this->setCampoCod('cod_registro');
    $this->setComplementoChave('seq_arquivo,exercicio,tipo_registro,cod_arquivo');

    $this->AddCampo('cod_registro', 'integer', true, '', true, true);
    $this->AddCampo('exercicio', 'varchar', true, '4', true, true);
    $this->AddCampo('tipo_registro', 'integer', true, '', true, true);
    $this->AddCampo('cod_arquivo', 'integer', true, '', true, true);
    $this->AddCampo('seq_arquivo', 'integer', true, '', true, true);
    $this->AddCampo('conta_orc_despesa', 'varchar', false, '50', false, false);
    $this->AddCampo('conta_orc_receita', 'varchar', true, '50', false, false);
    $this->AddCampo('conta_contabil', 'varchar', true, '50', false, false);
  }

  public function recuperaContasOrcamentariasDespesa(&$rsRecordSet, $exercicio, $grupo, $nomeArquivo, $excluidas, $boTransacao = "") {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContasOrcamentariasDespesa($exercicio, $grupo, $nomeArquivo, $excluidas);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
  }

  public function montaRecuperaContasOrcamentariasDespesa($exercicio, $grupo, $nomeArquivo, $excluidas) {
    $sql = "SELECT conta_despesa.cod_conta,
                   conta_despesa.cod_estrutural,
                   conta_despesa.descricao,
                   '" . $exercicio . "' AS exercicio,
                   '" . $grupo . "' AS grupo,
                   '" . $nomeArquivo . "' AS nome_arquivo
            FROM orcamento.conta_despesa
            WHERE conta_despesa.exercicio = '" . $exercicio . "'
                  AND conta_despesa.cod_estrutural LIKE('" . $grupo . "%') ";
    if (!empty($excluidas)) {
      $sql.= "AND conta_despesa.cod_conta NOT IN (" . implode(',', $excluidas) . ") ";
    }
    $sql.= "ORDER BY conta_despesa.cod_estrutural";
    return $sql;
  }

  public function recuperaContasOrcamentariasReceita(&$rsRecordSet, $exercicio, $grupo, $nomeArquivo, $excluidas, $boTransacao = "") {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContasOrcamentariasReceita($exercicio, $grupo, $nomeArquivo, $excluidas);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
  }

  public function montaRecuperaContasOrcamentariasReceita($exercicio, $grupo, $nomeArquivo, $excluidas) {
    $sql = "SELECT conta_receita.cod_conta,
                   conta_receita.cod_estrutural,
                   conta_receita.descricao,
                   '" . $exercicio . "' AS exercicio,
                   '" . $grupo . "' AS grupo,
                   '" . $nomeArquivo . "' AS nome_arquivo
            FROM orcamento.conta_receita
            WHERE conta_receita.exercicio = '" . $exercicio . "'
                  AND conta_receita.cod_estrutural LIKE('" . $grupo . "%') ";
    if (!empty($excluidas)) {
      $sql.= "AND conta_receita.cod_conta NOT IN (" . implode(',', $excluidas) . ") ";
    }
    $sql.= "ORDER BY conta_receita.cod_estrutural";
    return $sql;
  }

  public function recuperaContasContabeis(&$rsRecordSet, $exercicio, $grupo, $nomeArquivo, $excluidas, $boTransacao = "") {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaContasContabeis($exercicio, $grupo, $nomeArquivo, $excluidas);
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
  }

  public function montaRecuperaContasContabeis($exercicio, $grupo, $nomeArquivo, $excluidas) {
    $sql = "SELECT plano_conta.cod_conta,
                   plano_conta.cod_estrutural,
                   plano_conta.nom_conta,
                   '" . $exercicio . "' AS exercicio,
                   '" . $grupo . "' AS grupo,
                   '" . $nomeArquivo . "' AS nome_arquivo
            FROM contabilidade.plano_conta
            WHERE plano_conta.exercicio = '" . $exercicio . "'
                  AND plano_conta.cod_estrutural LIKE('" . $grupo . "%') ";
    if (!empty($excluidas)) {
      $sql.= "AND plano_conta.cod_conta NOT IN (" . implode(',', $excluidas) . ") ";
    }
    $sql.= "ORDER BY plano_conta.cod_estrutural";
    return $sql;
  }

  public function __destruct(){}

}
