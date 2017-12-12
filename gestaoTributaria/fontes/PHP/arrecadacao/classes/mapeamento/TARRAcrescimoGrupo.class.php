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
  * Classe de mapeamento da tabela ARRECADACAO.ACRESCIMO_GRUPO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRAcrescimoGrupo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.7  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.ACRESCIMO_GRUPO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRAcrescimoGrupo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRAcrescimoGrupo()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.acrescimo_grupo');

    $this->setCampoCod('cod_acrescimo');
    $this->setComplementoChave('cod_acrescimo,cod_grupo, cod_tipo');

    $this->AddCampo('cod_acrescimo','integer',true,'',true,true);
    $this->AddCampo('cod_grupo','integer',true,'',false,true);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('ano_exercicio', 'varchar', true, '4', true, true );
}
function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                       \r\n";
    $stSql .= "    aag.cod_acrescimo,                       \r\n";
    $stSql .= "    aag.cod_grupo,                           \r\n";
    $stSql .= "    aag.cod_tipo,                            \r\n";
    $stSql .= "    aag.ano_exercicio,                       \r\n";
    $stSql .= "    ma.descricao_acrescimo                   \r\n";
    $stSql .= "FROM                                         \r\n";
    $stSql .= "    arrecadacao.acrescimo_grupo as aag,      \r\n";
    $stSql .= "    monetario.acrescimo as ma                \r\n";
    $stSql .= "WHERE                                        \r\n";
    $stSql .= "    aag.cod_acrescimo = ma.cod_acrescimo     \r\n";

return $stSql;
}

}
?>
