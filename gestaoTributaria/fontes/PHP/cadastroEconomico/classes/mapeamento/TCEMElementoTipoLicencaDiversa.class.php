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
  * Classe de mapeamento da tabela ECONOMICO.ELEMENTO_TIPO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMElementoTipoLicencaDiversa.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.11
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ELEMENTO_TIPO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMElementoTipoLicencaDiversa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMElementoTipoLicencaDiversa()
{
    parent::Persistente();
    $this->setTabela('economico.elemento_tipo_licenca_diversa');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_elemento,cod_tipo');

    $this->AddCampo('cod_elemento','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('ativo','boolean',true,'',false,false);

}
function recuperaElementoTipoLicencaDiversa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoTipoLicencaDiversa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function recuperaElementoTipoLicencaDiversaSelecionados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao =
"") {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoTipoLicencaDiversaSelecionados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaElementoTipoLicencaDiversaDisponiveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "
") {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoTipoLicencaDiversaDisponiveis().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaElementoTipoLicencaDiversa()
{
    $stSql  ="    SELECT                                            \n";
    $stSql .="        EL.cod_elemento,                              \n";
    $stSql .="        EL.nom_elemento                               \n";
    $stSql .="    FROM                                              \n";
    $stSql .="        economico.elemento as EL,                       \n";
    $stSql .="        economico.tipo_licenca_diversa as A,            \n";
    $stSql .="        economico.elemento_tipo_licenca_diversa as AE   \n";
    $stSql .="    WHERE                                             \n";
    $stSql .="        A.cod_tipo = AE.cod_tipo AND                  \n";
    $stSql .="        AE.cod_elemento = EL.cod_elemento             \n";

    return $stSql;
}
function montaRecuperaElementoTipoLicencaDiversaSelecionados()
{
    $stSql  ="    SELECT                                              \n ";
    $stSql .="        EL.COD_ELEMENTO,                                \n ";
    $stSql .="        EL.NOM_ELEMENTO                                 \n ";
    $stSql .="    FROM                                                \n ";
    $stSql .="        economico.elemento AS EL                          \n ";
    $stSql .="    LEFT JOIN                                           \n ";
    $stSql .="        economico.elemento_tipo_licenca_diversa AS AE     \n ";
    $stSql .="    ON                                                  \n ";
    $stSql .="        EL.COD_ELEMENTO = AE.COD_ELEMENTO               \n ";
    $stSql .="    WHERE                                               \n ";
    $stSql .="        AE.COD_ELEMENTO IS NOT NULL                     \n ";

    return $stSql;
}

function montaRecuperaElementoTipoLicencaDiversaDisponiveis()
{
    $stSql  ="    SELECT                                              \n ";
    $stSql .="        EL.COD_ELEMENTO,                                \n ";
    $stSql .="        EL.NOM_ELEMENTO                                 \n ";
    $stSql .="    FROM                                                \n ";
    $stSql .="        economico.elemento AS EL                          \n ";
    $stSql .="    LEFT JOIN                                           \n ";
    $stSql .="        economico.baixa_elemento AS BE                    \n ";
    $stSql .="    ON                                                  \n ";
    $stSql .="        EL.COD_ELEMENTO = BE.COD_ELEMENTO               \n ";
    $stSql .="    WHERE                                               \n ";
    $stSql .="        BE.COD_ELEMENTO IS NULL AND                     \n ";

    return $stSql;

}
}
