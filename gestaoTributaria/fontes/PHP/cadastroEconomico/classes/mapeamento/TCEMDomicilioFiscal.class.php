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
  * Classe de mapeamento da tabela ECONOMICO.DOMICILIO_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMDomicilioFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.DOMICILIO_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMDomicilioFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMDomicilioFiscal()
{
    parent::Persistente();
    $this->setTabela('economico.domicilio_fiscal');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,timestamp');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('inscricao_municipal','integer',true,'',false,true);

}

function recuperaDomicilioFiscal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDomicilioFiscal().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDomicilioFiscal()
{
    $stSql  = "SELECT                                                                   \n";
    $stSql .= "    df.inscricao_economica,                                              \n";
    $stSql .= "    df.inscricao_municipal,                                              \n";
    $stSql .= "    nl.nom_logradouro,                                                   \n";
    $stSql .= "    i.numero,                                                            \n";
    $stSql .= "    i.complemento                                                        \n";
    $stSql .= "FROM                                                                     \n";
    $stSql .= "    economico.domicilio_fiscal as df,                                    \n";
    $stSql .= "    economico.cadastro_economico as ce,                                  \n";
    $stSql .= "    imobiliario.imovel i,                                                \n";
    $stSql .= "    imobiliario.imovel_confrontacao ic,                                  \n";
    $stSql .= "    imobiliario.confrontacao_trecho ct,                                  \n";
    $stSql .= "    imobiliario.trecho t,                                                \n";
    $stSql .= "    sw_logradouro l,                                                     \n";
    $stSql .= "    sw_nome_logradouro nl                                                \n";
    $stSql .= "WHERE                                                                    \n";
    $stSql .= "    ce.inscricao_economica  = df.inscricao_economica    AND              \n";
    $stSql .= "    i.inscricao_municipal   = df.inscricao_municipal    AND              \n";
    $stSql .= "    ic.inscricao_municipal  = i.inscricao_municipal     AND              \n";
    $stSql .= "    ct.cod_confrontacao     = ic.cod_confrontacao       AND              \n";
    $stSql .= "    ct.cod_lote             = ic.cod_lote               AND              \n";
    $stSql .= "    t.cod_trecho            = ct.cod_trecho             AND              \n";
    $stSql .= "    t.cod_logradouro        = ct.cod_logradouro         AND              \n";
    $stSql .= "    l.cod_logradouro        = t.cod_logradouro          AND              \n";
    $stSql .= "    nl.cod_logradouro       = l.cod_logradouro                           \n";

    return $stSql;
}
}
