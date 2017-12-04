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
    * Classe de mapeamento da tabela CONTABILIDADE.DESDOBRAMENTO_RECEITA
    * Data de Criação: 14/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.7  2006/10/18 18:18:14  cako
Bug #7241#

Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CONTABILIDADE.DESDOBRAMENTO_RECEITA
  * Data de Criação: 14/02/2005

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TContabilidadeDesdobramentoReceita extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TContabilidadeDesdobramentoReceita()
{
    parent::Persistente();
    $this->setTabela('contabilidade.desdobramento_receita');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_receita_principal, cod_receita_secundaria');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_receita_principal','integer',true,'',true,true);
    $this->AddCampo('cod_receita_secundaria','integer',true,'',true,true);
    $this->AddCampo('percentual','numeric',true,'4,2',true,true);

}

function verificaReceitaSecundaria(&$boSecundaria, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaReceitaSecundaria();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $boSecundaria = $rsRecordSet->getCampo('cod_receita_secundaria');

    return $obErro;
}

function montaVerificaReceitaSecundaria()
{
    $stSql .= " SELECT * FROM ".$this->getTabela()."                               \n";
    $stSql .= " WHERE exercicio   = '". $this->getDado('exercicio')."'               \n";
    $stSql .= " AND   cod_receita_secundaria = ". $this->getDado('cod_receita')."  \n";

    return $stSql;
}

}
