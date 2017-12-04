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
  * Classe de mapeamento da tabela economico.atividade_CNAE_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMAtividadeCnaeFiscal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  economico.atividade_CNAE_FISCAL
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMAtividadeCnaeFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMAtividadeCnaeFiscal()
{
    parent::Persistente();
    $this->setTabela('economico.atividade_cnae_fiscal');

    $this->setCampoCod('cod_atividade');
    $this->setComplementoChave('');

    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('cod_cnae','integer',true,'',false,true);

}

function recuperaAtividadeCnae(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtividadeCnae().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtividadeCnae()
{
    $stSql .="    SELECT                                      \n";
    $stSql .="        CF.nom_atividade as nom_atividade_cnae, \n";
    $stSql .="        AT.nom_atividade,                       \n";
    $stSql .="        CF.cod_cnae,                            \n";
    $stSql .="        AT.cod_atividade                        \n";
    $stSql .="    FROM                                        \n";
    $stSql .="        economico.atividade AS AT,                \n";
    $stSql .="        economico.atividade_cnae_fiscal AS AC,    \n";
    $stSql .="        economico.cnae_fiscal as CF               \n";
    $stSql .="    WHERE                                       \n";
    $stSql .="        AT.cod_atividade = AC.cod_atividade AND \n";
    $stSql .="        AC.cod_cnae      = CF.cod_cnae          \n";

    return $stSql;
}

}
