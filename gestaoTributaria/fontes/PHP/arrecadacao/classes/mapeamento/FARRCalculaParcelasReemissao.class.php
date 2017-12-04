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
    * Classe de mapeamento da função ARRECADACAO.ABRE_CALCULO()
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRCalculaParcelasReemissao.class.php 61643 2015-02-20 10:45:39Z evandro $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.5  2006/12/18 17:57:21  dibueno
Alteração do Caso de Uso

Revision 1.4  2006/12/12 15:20:13  cercato
funcao para retornar multa e juros.

Revision 1.3  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.2  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 12/05/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Lucas Teixeira Stephanou

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRCalculaParcelasReemissao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FARRCalculaParcelasReemissao()
{
    parent::Persistente();
    $this->AddCampo('valor','varchar'  ,false       ,''     ,false   ,false );
}

function executaFuncao(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaFuncao($stParametros)
{
    $stSql  = " SELECT                                                              \r\n";
    $stSql .= "     arrecadacao.calculaParcelasReemissao(".$stParametros.") as valor  \r\n";

    return $stSql;
}

function executaCalculaValoresParcelasReemissao(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaCalculaValoresParcelasReemissao($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaCalculaValoresParcelasReemissao($stParametros)
{
    $stSql  = " SELECT                                                              \r\n";
    $stSql .= "     arrecadacao.calculaValoresParcelasReemissao(".$stParametros.") as valor  \r\n";

    return $stSql;
}

function executaCalculaValoresParcelasCobranca(&$rsRecordset, $stParametros,$boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaCalculaValoresParcelasCobranca($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

    return $obErro;
}

function montaExecutaCalculaValoresParcelasCobranca($stParametros)
{
    $stSql  = " SELECT arrecadacao.calculaValoresParcelasCobranca(".$stParametros.") as valor  \r\n";

    return $stSql;
}




}
?>
