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
    * Classe de mapeamento da tabela RECIBO_EXTRA
    * Data de Criação: 01/08/2006

    * @author Analista: Cleisson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: bruce $
    $Date: 2006-09-14 07:00:54 -0300 (Qui, 14 Set 2006) $

    * Casos de uso: uc-02.04.30, uc-02.04.27, 02.04.29
*/

/*
$Log$
Revision 1.2  2006/09/14 10:00:54  bruce
colocado mais um numero de UC

Revision 1.1  2006/09/13 16:14:07  bruce
Desenvolvimento

Revision 1.7  2006/09/13 10:01:59  bruce
Desenvolvimento

Revision 1.6  2006/09/12 16:39:00  cako
implementaçao do uc-02.04.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaReciboExtraAnulacao   extends Persistente
{
   public function TTesourariaReciboExtraAnulacao()
   {
        parent::Persistente();

        $this->setTabela( 'tesouraria.recibo_extra_anulacao' );
        $this->setCampoCod ( '' );
        $this->setComplementoChave ( 'cod_recibo_extra, exercicio, cod_entidade, tipo_recibo' );

        $this->addCampo('cod_entidade'     , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('exercicio'        , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('cod_recibo_extra' , 'integer'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('tipo_recibo'      , 'varchar'  ,true  , ''  , true, TTesourariaReciboExtra );
        $this->addCampo('timestamp_anulacao' , 'timestamp',true  , ''  , false , false );

   }
}
