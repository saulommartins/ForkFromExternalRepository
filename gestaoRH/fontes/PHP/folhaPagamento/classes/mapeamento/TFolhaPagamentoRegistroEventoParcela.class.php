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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.REGISTRO_EVENTO_PARCELA
    * Data de Criação: 04/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.REGISTRO_EVENTO_PARCELA
  * Data de Criação: 04/11/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoRegistroEventoParcela extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoRegistroEventoParcela()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.registro_evento_parcela');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_registro,timestamp,cod_evento');

    $this->AddCampo( 'cod_registro', 'integer'  ,true , '', true , 'TFolhaPagamentoUltimoRegistroEvento' );
    $this->AddCampo( 'timestamp'   , 'timestamp',false, '', true , 'TFolhaPagamentoUltimoRegistroEvento' );
    $this->AddCampo( 'cod_evento'  , 'integer'  ,true , '', true , 'TFolhaPagamentoUltimoRegistroEvento' );
    $this->AddCampo( 'parcela'     , 'integer'  ,true , '', false, false                                 );
    $this->AddCampo( 'mes_carencia', 'integer'  ,true , '', false, false                                 );
}
}
