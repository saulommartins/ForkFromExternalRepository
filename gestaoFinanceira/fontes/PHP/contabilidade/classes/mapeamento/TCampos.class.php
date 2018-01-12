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
include_once (CLA_PERSISTENTE);

class TCampos extends Persistente {
  /**
    * Método Construtor
    * @access Public
  */
  public function __construct() {
    parent::Persistente();
  }

  /**
    * @access Public
    * @param Object $rsRecordSet Objeto RecordSet
    * @param String $stCondicao String de condição do SQL (WHERE)
    * @param String $stOrdem String de Ordenação do SQL (ORDER BY)
    * @param Boolean $boTransacao
    * @return Object Objeto Erro
  */
  function recuperaRegistros(&$rsRecordSet, $exercicio, $nomeCampo, $nomeTag, $nomeArquivo, $boTransacao = "") {
    $erro = new Erro;
    $conexao = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRegistros($exercicio, $nomeCampo, $nomeTag, $nomeArquivo);
    $this->setDebug($stSql);
    $obErro = $conexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $erro;
  }

  function montaRecuperaRegistros($exercicio, $nomeCampo, $nomeTag, $nomeArquivo) {
    $sql = "SELECT dcasp.nome_tag,
                   dcasp.nome_campo,
                   dcasp.nome_arquivo_pertencente,
                   dcasp.grupo,
                   dcasp.tipo_registro,
                   dcasp.cod_arquivo,
                   dcasp.seq_arquivo
            FROM tcemg.configuracao_dcasp_arquivo AS dcasp
            WHERE dcasp.exercicio = '" . $exercicio . "'
                  AND dcasp.nome_arquivo_pertencente = '" . $nomeArquivo . "'";
    if (!empty($nomeCampo)) {
      $sql.= "AND dcasp.nome_campo LIKE ('%" . $nomeCampo . "%') ";
    }
    if (!empty($nomeTag)) {
      $sql.= "AND dcasp.nome_tag LIKE ('%" . $nomeTag . "%') ";
    }

    return $sql;
  }

}
