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
    * Classe de mapeamento da tabela folhapagamento.tipo_evento_configuracao_media
    * Data de Criação: 04/07/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.tipo_evento_configuracao_media
  * Data de Criação: 04/07/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoTipoEventoConfiguracaoMedia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoTipoEventoConfiguracaoMedia()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.tipo_evento_configuracao_media");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao,timestamp,cod_evento,cod_caso');

    $this->AddCampo('cod_configuracao','integer',true,'',true   ,'TFolhaPagamentoConfiguracaoEventoCaso');
    $this->AddCampo('timestamp','timestamp',false,'',true       ,'TFolhaPagamentoConfiguracaoEventoCaso');
    $this->AddCampo('cod_evento','integer',true,'',true         ,'TFolhaPagamentoConfiguracaoEventoCaso');
    $this->AddCampo('cod_caso','integer',true,'',true           ,'TFolhaPagamentoConfiguracaoEventoCaso');
    $this->AddCampo('cod_tipo','integer',true,'',false          ,'TFolhaPagamentoTipoMedia');

}
}
