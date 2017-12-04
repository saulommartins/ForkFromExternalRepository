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
$Date: 2008-03-12 14:33:55 -0300 (Qua, 12 Mar 2008) $

* Casos de uso: uc-04.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SINDICATO
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSindicato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoSindicato()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.sindicato');

    $this->setCampoCod('numcgm');
    $this->setComplementoChave('');

    $this->AddCampo('numcgm','INTEGER',true,'',true,true);
    $this->AddCampo('data_base','INTEGER',true,'',false,false);
    $this->AddCampo('cod_evento','INTEGER',true,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stQuebra = "\n";

    $stSQL .= "SELECT                                                                           $stQuebra ";
    $stSQL .= "     S.numcgm,                                                                   $stQuebra ";
    $stSQL .= "     S.data_base,                                                                $stQuebra ";
    $stSQL .= "     S.cod_evento,                                                               $stQuebra ";
    $stSQL .= "     CGM.nom_cgm,                                                                $stQuebra ";
    $stSQL .= "     e.descricao as descricao_evento                                             $stQuebra ";
    $stSQL .= "FROM sw_cgm                AS CGM                                                $stQuebra ";
    $stSQL .= "inner join folhapagamento.sindicato as s  on ( s.numcgm = CGM.numcgm       )     $stQuebra ";
    $stSQL .= "inner join folhapagamento.evento    as e  on ( s.cod_evento = e.cod_evento )     $stQuebra ";

    return $stSQL;
}

function RecuperaCGMSindicato(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCGMSindicato().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCGMSindicato()
{
    $stSql .= "SELECT sindicato.numcgm                                                      \n";
    $stSql .= "     , sindicato.data_base                                                   \n";
    $stSql .= "     , sindicato.cod_evento                                                  \n";
    $stSql .= "     , contrato_servidor_sindicato.numcgm_sindicato as sindicato_servidor    \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                        \n";
    $stSql .= "  FROM folhapagamento.sindicato                    \n";
    $stSql .= "     , sw_cgm                                                                \n";
    $stSql .= "LEFT JOIN pessoal.contrato_servidor_sindicato      \n";
    $stSql .= "       ON sw_cgm.numcgm = contrato_servidor_sindicato.numcgm_sindicato       \n";
    $stSql .= " WHERE sindicato.numcgm = sw_cgm.numcgm                                      \n";
    $stSql .= "   AND sindicato.numcgm = ".$this->getDado('numcgm')."                       \n";
    $stSql .= "LIMIT 1                                                                      \n";

    return $stSql;

}

}
