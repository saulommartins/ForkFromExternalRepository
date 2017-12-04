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
  * Classe de mapeamento da tabela ECONOMICO.PROCESSO_ATIVIDADE_CAD_ECON
  * Data de Criação: 13/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMProcessoAtividadeCadEcon.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.3  2006/11/20 09:50:17  cercato
bug #7438#

Revision 1.2  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.PROCESSO_ATIVIDADE_CAD_ECON
  * Data de Criação: 13/03/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Fernando Piccini Cercato

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMProcessoAtividadeCadEcon extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMProcessoAtividadeCadEcon()
{
    parent::Persistente();
    $this->setTabela('economico.processo_atividade_cad_econ');

    $this->setCampoCod('');
    $this->setComplementoChave('ocorrencia_atividade,cod_atividade,inscricao_economica,ano_exercicio,cod_processo');
    //ocorrencia_atividade, cod_atividade, inscricao_economica, ano_exercicio, cod_processo, timestamp(nao fk)
    $this->AddCampo('ocorrencia_atividade','integer',true,'',true,true);
    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('ano_exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_processo','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
}

function recuperaListaProcessos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaProcessos().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaProcessos()
{
    $stSQL  =" SELECT DISTINCT \n";
    $stSQL .="     cod_processo, \n";
    $stSQL .="     ano_exercicio, \n";
    $stSQL .="     ocorrencia_atividade, \n";
    $stSQL .="     inscricao_economica, \n";
    $stSQL .="     timestamp \n";
    $stSQL .=" FROM \n";
    $stSQL .="     economico.processo_atividade_cad_econ \n";

    return $stSQL;
}

}
