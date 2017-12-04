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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.1  2007/10/10 15:39:29  bruce
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOObras extends Persistente
{
    public function TTGOObras()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.obra");

        $this->setCampoCod('cod_obra');
        $this->setComplementoChave('ano_obra');

        $this->AddCampo('cod_obra'          ,'integer' ,true, ''    , true , true);
        $this->AddCampo('ano_obra'          ,'integer' ,true, ''    , true , false);
        $this->AddCampo('especificacao'     ,'varchar' ,true, '100' , true , false);
        $this->AddCampo('cod_unidade'       ,'integer' ,false, ''   , false, true);
        $this->AddCampo('cod_grandeza'      ,'integer' ,false, ''   , false, true);
        $this->AddCampo('quantidade'        ,'integer' ,false, ''   , false, false);
        $this->AddCampo('endereco'          ,'varchar' ,false, '100', false, false);
        $this->AddCampo('bairro'            ,'varchar' ,false, '40' , false, false);
        $this->AddCampo('fiscal'            ,'varchar' ,false, '50' , false, false);
        $this->AddCampo('grau_latitude'     ,'integer' ,false, ''   , false, false);
        $this->AddCampo('minuto_latitude'   ,'integer' ,false, ''   , false, false);
        $this->AddCampo('segundo_latitude'  ,'numeric' ,false, '4,2', false, false);
        $this->AddCampo('grau_longitude'    ,'integer' ,false, ''   , false, false);
        $this->AddCampo('minuto_longitude'  ,'integer' ,false, ''   , false, false);
        $this->AddCampo('segundo_longitude' ,'numeric' ,false, '4,2', false, false);

    }
}
