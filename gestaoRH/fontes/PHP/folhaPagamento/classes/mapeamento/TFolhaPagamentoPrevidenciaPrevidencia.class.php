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
  * Classe de mapeamento da tabela FOLHAPAGAMENTO.PREVIDENCIA
  * Data de Criação: 26/11/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

      Caso de uso: uc-04.05.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.PREVIDENCIA
  * Data de Criação: 26/11/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPrevidenciaPrevidencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoPrevidenciaPrevidencia()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.previdencia_previdencia');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_previdencia, timestamp');

    $this->AddCampo('cod_previdencia' , 'integer'  ,  true, ''   ,true  ,true);
    $this->AddCampo('descricao'       , 'varchar'  ,  true, 80   ,false ,false);
    $this->AddCampo('aliquota'        , 'numeric'  ,  true, '5.2',false ,false);
    $this->AddCampo('timestamp'       , 'timestamp', false, ''   ,false ,false);
    $this->AddCampo('tipo_previdencia', 'char'     ,  true, ''   ,false ,false);
    $this->AddCampo('vigencia'        , 'date'     ,  true, ''   ,false ,false);
}

function montaRecuperaTodos()
{
    $stSql .= " SELECT cod_previdencia                                            \n";
    $stSql .= "      , descricao                                                  \n";
    $stSql .= "      , aliquota                                                   \n";
    $stSql .= "      , TO_CHAR(timestamp,'yyyy-mm-dd hh24:mi:ss.us') AS timestamp \n";
    $stSql .= "      , tipo_previdencia                                           \n";
    $stSql .= "      , vigencia AS vigencia_ordenacao                             \n";
    $stSql .= "      , TO_CHAR(vigencia,'dd/mm/yyyy') AS vigencia                 \n";
    $stSql .= "   FROM folhapagamento.previdencia_previdencia                     \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT *                                                                                            \n";
    $stSql .= "  FROM folhapagamento.previdencia_previdencia                                                       \n";
    $stSql .= "     , (  SELECT cod_previdencia                                                                    \n";
    $stSql .= "               , max(timestamp) as timestamp                                                        \n";
    $stSql .= "            FROM folhapagamento.previdencia_previdencia                                             \n";
    $stSql .= "        GROUP BY cod_previdencia) as max_previdencia_previdencia                                    \n";
    $stSql .= " WHERE previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia        \n";
    $stSql .= "   AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp                    \n";

    return $stSql;
}

}
