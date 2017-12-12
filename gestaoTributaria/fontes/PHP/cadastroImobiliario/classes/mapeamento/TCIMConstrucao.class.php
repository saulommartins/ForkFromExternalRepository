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
     * Classe de mapeamento para a tabela IMOBILIARIO.CONSTRUCAO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMConstrucao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
                     uc-05.01.12
*/

/*
$Log$
Revision 1.5  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.CONSTRUCAO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMConstrucao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMConstrucao()
{
    parent::Persistente();
    $this->setTabela('imobiliario.construcao');

    $this->setCampoCod('cod_construcao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_construcao','integer',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaRelacionamentoProcesso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoProcesso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoProcesso()
{
    $stSQL .= " SELECT                                                      \n";
    $stSQL .= "     cp.cod_construcao   as cod_construcao,                  \n";
    $stSQL .= "     cp.cod_processo     as cod_processo,                    \n";
    $stSQL .= "     cp.exercicio        as exercicio,                       \n";
    $stSQL .= "     cpad(cp.cod_processo,5,'0') || '/' || cp.exercicio as cod_processo_ano,                      \n";
    $stSQL .= "     cp.timestamp as timestamp,                              \n";
    $stSQL .= "     to_char(cp.timestamp,'dd/mm/yyyy') as data,             \n";
    $stSQL .= "     to_char(cp.timestamp,'hh24:mi:ss') as hora              \n";
    $stSQL .= " FROM                                                        \n";
    $stSQL .= "         imobiliario.construcao_processo AS cp                   \n";

    return $stSQL;

}

function recuperaUnidadeAutonoma(&$rsRecordSet, $stCondicao = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUnidadeAutonoma().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUnidadeAutonoma()
{
    $stSQL .= " SELECT                                   \n";
    $stSQL .= "     cod_construcao                       \n";
    $stSQL .= " FROM                                     \n";
    $stSQL .= "         imobiliario.unidade_dependente       \n";
    $stSQL .= " WHERE                                    \n";

    return $stSQL;
}

}
