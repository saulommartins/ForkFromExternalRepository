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
    * Classe de mapeamento da tabela ALMOXARIFADO.LOCALIZACAO
    * Data de Criação: 30/01/2006

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Rodrigo D. Schreiner

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.03.14
*/

/*
$Log$
Revision 1.4  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:09:27  diego

*/

 include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
 include_once(CLA_PERSISTENTE                                                                     );

  class TAlmoxarifadoLocalizacaoFisica extends Persistente
  {
    /**
        * Método Construtor
        * @access Private
    */

   public function TAlmoxarifadoLocalizacaoFisica()
   {
    parent::Persistente();
    $this->setTabela          ('almoxarifado.localizacao_fisica'             );
    $this->setCampoCod        ('cod_localizacao'                             );
    $this->setComplementoChave('cod_almoxarifado'                            );
    $this->AddCampo           ('cod_almoxarifado','integer',true,'',true,true);
    $this->AddCampo           ('cod_localizacao','integer',true,'',true,false);
    $this->AddCampo           ('localizacao','varchar',true,'30',false,false );
   }
}
