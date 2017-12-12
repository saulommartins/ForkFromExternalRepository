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
    * Extensão da Classe de mapeamento TOrcamentoProjetoAtividade
    * Data de Criação: 22/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
    
    $Id: TTPBPao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/04/23 15:27:48  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/01/25 20:30:03  diego
Novos arquivos de exportação.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php" );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBPao extends TOrcamentoProjetoAtividade
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBPao()
{
    parent::TOrcamentoProjetoAtividade();
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = "    SELECT                                                                                   \n";
    $stSql .= "           pao.*                                                                             \n";
    $stSql .= "         , acao.num_acao                                                                     \n";
    $stSql .= "         , CASE orcamento.fn_consulta_tipo_pao('".$this->getDado('exercicio')."',pao.num_pao)\n";
    $stSql .= "             WHEN 1 THEN 1                                                                   \n";
    $stSql .= "             WHEN 3 THEN 1                                                                   \n";
    $stSql .= "             WHEN 5 THEN 1                                                                   \n";
    $stSql .= "             WHEN 7 THEN 1                                                                   \n";
    $stSql .= "             WHEN 9 THEN 1                                                                   \n";
    $stSql .= "             WHEN 2 THEN 2                                                                   \n";
    $stSql .= "             WHEN 4 THEN 2                                                                   \n";
    $stSql .= "             WHEN 6 THEN 2                                                                   \n";
    $stSql .= "             WHEN 8 THEN 2                                                                   \n";
    $stSql .= "             WHEN 0 THEN 0                                                                   \n";
    $stSql .= "           END as tipo_pao                                                                   \n";    
    $stSql .= "         , acao_dados.descricao AS meta                                                      \n";
    $stSql .= "         , unidade_medida.nom_unidade AS medida                                              \n";
    $stSql .= "      FROM                                                                                   \n";
    $stSql .= "           orcamento.pao                                                                     \n";
    
    $stSql .= "      JOIN orcamento.pao_ppa_acao                                                            \n";
    $stSql .= "        ON pao_ppa_acao.exercicio=pao.exercicio                                              \n";
    $stSql .= "       AND pao_ppa_acao.num_pao=pao.num_pao                                                  \n";

    $stSql .= "      JOIN ppa.acao                                                                          \n";
    $stSql .= "        ON acao.cod_acao=pao_ppa_acao.cod_acao                                               \n";

    $stSql .= "      JOIN ppa.acao_dados                                                                    \n";
    $stSql .= "        ON acao_dados.cod_acao=acao.cod_acao                                                 \n";
    $stSql .= "       AND acao_dados.timestamp_acao_dados=acao.ultimo_timestamp_acao_dados                  \n";

    $stSql .= "      JOIN administracao.unidade_medida                                                      \n";
    $stSql .= "        ON unidade_medida.cod_unidade=acao_dados.cod_unidade_medida                          \n";
    $stSql .= "       AND unidade_medida.cod_grandeza=acao_dados.cod_grandeza                               \n"; 
  //  $stSql .= "  ORDER BY num_pao                                                                         \n";
    return $stSql;
}
}

?>