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
    * Classe de mapeamento da tabela folhapagamento.previdencia_regime_rat
    * Data de Criação: 12/04/2006

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name:  $
      $Author: souzadl $
      $Date: 2007/06/05 19:56:04 $

      Caso de uso: uc-04.05.04
*/

/*
$Log: TFolhaPagamentoPrevidenciaRegimeRat.class.php,v $
Revision 1.25  2007/06/05 19:56:04  souzadl
Bug #9309#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.previdencia_regime_rat
  * Data de Criação: 12/04/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/

class TFolhaPagamentoPrevidenciaRegimeRat extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoPrevidenciaRegimeRat()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.previdencia_regime_rat");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_previdencia,timestamp');

    $this->AddCampo('cod_previdencia','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('aliquota_rat','numeric',true,'5,2',false,false);
    $this->AddCampo('aliquota_fap','numeric',true,'5,4',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT previdencia_regime_rat.*                                                                 \n";
    $stSql .= "  FROM folhapagamento.previdencia_regime_rat                                                    \n";
    $stSql .= "     , (SELECT cod_previdencia                                                                  \n";
    $stSql .= "             , max(timestamp) as timestamp                                                      \n";
    $stSql .= "          FROM folhapagamento.previdencia_regime_rat                                            \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_regime_rat                                 \n";
    $stSql .= "     , folhapagamento.previdencia                                                               \n";
    $stSql .= " WHERE previdencia_regime_rat.cod_previdencia = max_previdencia_regime_rat.cod_previdencia      \n";
    $stSql .= "   AND previdencia_regime_rat.timestamp = max_previdencia_regime_rat.timestamp                  \n";
    $stSql .= "   AND previdencia_regime_rat.cod_previdencia = previdencia.cod_previdencia                     \n";

    return $stSql;
}

function recuperaAliquotaSefip(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaAliquotaSefip();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAliquotaSefip()
{
    $stSql .= "SELECT previdencia_regime_rat.*                                                                 \n";
    $stSql .= "  FROM folhapagamento.previdencia_regime_rat                                                    \n";
    $stSql .= "     , (SELECT cod_previdencia                                                                  \n";
    $stSql .= "             , max(previdencia_regime_rat.timestamp) as timestamp                               \n";
    $stSql .= "          FROM folhapagamento.previdencia_regime_rat                                            \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_regime_rat                                 \n";
    $stSql .= "     , folhapagamento.previdencia                                                               \n";
    $stSql .= "     , folhapagamento.previdencia_previdencia                                                   \n";
    $stSql .= "     , (SELECT cod_previdencia                                                                  \n";
    $stSql .= "             , max(previdencia_previdencia.timestamp) as timestamp                              \n";
    $stSql .= "          FROM folhapagamento.previdencia_previdencia                                           \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_previdencia                                \n";
    $stSql .= " WHERE previdencia_regime_rat.cod_previdencia = max_previdencia_regime_rat.cod_previdencia      \n";
    $stSql .= "   AND previdencia_regime_rat.timestamp = max_previdencia_regime_rat.timestamp                  \n";
    $stSql .= "   AND previdencia_regime_rat.cod_previdencia = previdencia.cod_previdencia                     \n";
    $stSql .= "   AND previdencia.cod_previdencia = previdencia_previdencia.cod_previdencia                    \n";
    $stSql .= "   AND previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia    \n";
    $stSql .= "   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                \n";
    $stSql .= "   AND previdencia.cod_regime_previdencia = 1                                                   \n";
    $stSql .= "ORDER BY vigencia desc limit 1                                                                  \n";

    return $stSql;
}

}
?>
