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
* Classe de mapeamento da tabela PESSOAL.SINDICATO
* Data de Criação: 14/12/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SINDICATO_FUNCAO
  * Data de Criação: 14/04/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSindicatoFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoSindicatoFuncao()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.sindicato_funcao');

    $this->setCampoCod('numcgm');
    $this->setComplementoChave('');

    $this->AddCampo('numcgm',         'INTEGER', true,  '', true,  true);
    $this->AddCampo('cod_funcao',     'INTEGER', true,  '', false, true);
    $this->AddCampo('cod_modulo',     'INTEGER', true,  '', false, true);
    $this->AddCampo('cod_biblioteca', 'INTEGER', true,  '', false, true);
}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";
    $stSQL .= "SELECT                               $stQuebra";
    $stSQL .= "     S.numcgm,                       $stQuebra";
    $stSQL .= "     CGM.nom_cgm                     $stQuebra";
    $stSQL .= "FROM                                 $stQuebra";
    $stSQL .= "     ".$this->getTabela()." AS S,    $stQuebra";
    $stSQL .= "     sw_cgm                AS CGM   $stQuebra";
    $stSQL .= "WHERE                                $stQuebra";
    $stSQL .= "     S.numcgm = CGM.numcgm           $stQuebra";

    return $stSQL;
}

function RecuperaCGMSindicato(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCGMSindicato().$stFiltro.$stOrdem;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCGMSindicato()
{
    $stQuebra = "\n";
    $stSQL .= "SELECT                                                       $stQuebra";
    $stSQL .= "     S.numcgm,                                               $stQuebra";
    $stSQL .= "     CGM.nom_cgm                                             $stQuebra";
    $stSQL .= "FROM                                                         $stQuebra";
    $stSQL .= "     ".$this->getTabela()." AS S,                            $stQuebra";
    $stSQL .= "     sw_cgm                AS CGM                           $stQuebra";
    $stSQL .= "WHERE                                                        $stQuebra";
    $stSQL .= "     S.numcgm = CGM.numcgm and                               $stQuebra";
    $stSQL .= "     S.numcgm = ".$this->getDado('numcgm')."   $stQuebra";

    return $stSQL;

}

}
