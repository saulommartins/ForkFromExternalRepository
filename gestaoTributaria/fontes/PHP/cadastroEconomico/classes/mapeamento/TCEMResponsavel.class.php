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
  * Classe de mapeamento da tabela ECONOMICO.RESPONSAVEL
  * Data de Criação: 10/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMResponsavel.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.3  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.RESPONSAVEL
  * Data de Criação: 10/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMResponsavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMResponsavel()
{
    parent::Persistente();
    $this->setTabela('economico.responsavel');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,false);
}

function recuperaSequencia(&$rsRecordSet, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSequencia().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaSequencia()
{
    $stSql  = " SELECT max(sequencia) as max_sequencia          \n";
    $stSql .= " FROM                                            \n";
    $stSql .= "     economico.responsavel                       \n";

    return $stSql;
}

}
