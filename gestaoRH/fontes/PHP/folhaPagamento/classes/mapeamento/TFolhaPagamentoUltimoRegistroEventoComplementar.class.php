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
    * Classe de mapeamento da tabela folhapagamento.ultimo_registro_evento_complementar
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.ultimo_registro_evento_complementar
  * Data de Criação: 20/01/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoUltimoRegistroEventoComplementar extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoUltimoRegistroEventoComplementar()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.ultimo_registro_evento_complementar");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,cod_evento,cod_configuracao');

    $this->AddCampo('timestamp','timestamp',false,'',true      ,"TFolhaPagamentoRegistroEventoComplementar");
    $this->AddCampo('cod_registro','integer',true,'',true      ,"TFolhaPagamentoRegistroEventoComplementar");
    $this->AddCampo('cod_evento','integer',true,'',true        ,"TFolhaPagamentoRegistroEventoComplementar");
    $this->AddCampo('cod_configuracao','integer',true,'',true  ,"TFolhaPagamentoRegistroEventoComplementar");

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT ultimo_registro_evento_complementar.*                                                                        \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_complementar                                                           \n";
    $stSql .= "     , folhapagamento.registro_evento_complementar                                                                  \n";
    $stSql .= " WHERE ultimo_registro_evento_complementar.cod_registro = registro_evento_complementar.cod_registro                 \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.cod_evento = registro_evento_complementar.cod_evento                     \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.cod_configuracao = registro_evento_complementar.cod_configuracao         \n";
    $stSql .= "   AND ultimo_registro_evento_complementar.timestamp = registro_evento_complementar.timestamp                       \n";

    return $stSql;
}

function deletarUltimoRegistroEvento($boTransacao="")
{
    return $this->executaRecupera("montaDeletarUltimoRegistro", $rsRecordSet, "", "", $boTransacao);
}

function montaDeletarUltimoRegistro()
{
    $stSql  = "SELECT criarBufferTexto('stEntidade','".Sessao::getEntidade()."');       \n";
    $stSql .= "SELECT criarBufferTexto('stTipoFolha','C');                              \n";
    $stSql .= "SELECT deletarUltimoRegistroEvento(".$this->getDado("cod_registro")."    \n";
    $stSql .= "                                  ,".$this->getDado("cod_evento")."      \n";
    $stSql .= "                                 ,'".$this->getDado("cod_configuracao")."'  \n";
    $stSql .= "                                 ,'".$this->getDado("timestamp")."');    \n";

    return $stSql;
}

}
