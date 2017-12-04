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
    * Classe de mapeamento da tabela CONTABILIDADE.CLASSIFICACAO_CONTABIL
    * Data de Criação: 01/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.CLASSIFICACAO_CONTABIL
  * Data de Criação: 01/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeClassificacaoContabil extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TContabilidadeClassificacaoContabil()
    {
        parent::Persistente();
        $this->setTabela('contabilidade.classificacao_contabil');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_classificacao,exercicio');

        $this->AddCampo('cod_classificacao','integer',true,'',true,false);
        $this->AddCampo('exercicio','char',true,'4',true,false);
        $this->AddCampo('nom_classificacao','varchar',true,'80',false,false);

    }

        /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método
        * recuperaUltimoExercicio.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaUltimoExercicio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaUltimoExercicio().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaUltimoExercicio()
    {
        $stSQL  = "  SELECT classificacao_contabil.cod_classificacao                       \n";
        $stSQL .= "       , classificacao_contabil.nom_classificacao                       \n";
        $stSQL .= "       , MAX(classificacao_contabil.exercicio) AS exercicio             \n";
        $stSQL .= "    FROM contabilidade.classificacao_contabil                           \n";
        $stSQL .= "GROUP BY classificacao_contabil.cod_classificacao                       \n";
        $stSQL .= "       , classificacao_contabil.nom_classificacao                       \n";

        return $stSQL;
    }

}
