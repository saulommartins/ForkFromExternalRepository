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
     * Classe de mapeamento para a tabela IMOBILIARIO.PROPRIETARIO
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

     * $Id: TCIMProprietario.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.09
                     uc-05.01.17
*/

/*
$Log$
Revision 1.9  2006/12/07 11:57:40  cassiano
Bug #7739#

Revision 1.8  2006/11/23 15:54:55  cassiano
#ga_1.35.4#

Revision 1.7  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.PROPRIETARIO
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMProprietario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMProprietario()
{
    parent::Persistente();
    $this->setTabela('imobiliario.proprietario');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,inscricao_municipal');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('ordem','integer',true,'',false,false);
    $this->AddCampo('promitente','boolean',true,'',false,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('cota','numeric',true,'5,2',false,false);

}
function recuperaProprietariosCalculo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProprietariosCalculo($stCondicao);
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProprietariosCalculo($stFiltro = 0)
{
    $stSql = " SELECT * FROM imobiliario.fn_proprietarios_imovel(".$stFiltro.") as proprietarios(numcgm int)";

    return $stSql;
}

function recuperaProprietarioProcesso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProprietarioProcesso();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaProprietarioProcesso()
{
    $stSQL =  " SELECT                                                                   \n";
    $stSQL .= "     max(cota) as cota                                                    \n";
    $stSQL .= "     ,nom_cgm                                                             \n";
    $stSQL .= "     ,sw_cgm.numcgm                                                       \n";
    $stSQL .= "     ,inscricao_municipal                                                 \n";
    $stSQL .= " from                                                                     \n";
    $stSQL .= "     imobiliario.proprietario,                                            \n";
    $stSQL .= "     sw_cgm                                                               \n";
    $stSQL .= " where                                                                    \n";
    $stSQL .= "     imobiliario.proprietario.numcgm = sw_cgm.numcgm AND                  \n";
    $stSQL .= "     imobiliario.proprietario.inscricao_municipal = ".$this->getDado('inscricao_municipal')." \n";
    $stSQL .= " group by                                                                 \n";
    $stSQL .= "     sw_cgm.nom_cgm,                                                      \n";
    $stSQL .= "     sw_cgm.numcgm,                                                       \n";
    $stSQL .= "     imobiliario.proprietario.inscricao_municipal                         \n";
    $stSQL .= " order by                                                                 \n";
    $stSQL .= "     cota desc,                                                           \n";
    $stSQL .= "     sw_cgm.nom_cgm                                                       \n";

    return $stSQL;
}

}// end of class
