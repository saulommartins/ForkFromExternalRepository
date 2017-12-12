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
* Componente ISelectTipoObjeto

* Data de Criação: 04/10/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

Casos de uso: uc-03.04.33

*/

include_once ( CLA_SELECT );

class ISelectTipoObjeto extends Select
{
    public function ISelectTipoObjeto()
    {
        parent::Select();

        include_once(CAM_GP_COM_MAPEAMENTO . "TComprasTipoObjeto.class.php");
        $obMapeamento   = new TComprasTipoObjeto();
        $rsRecordSet    = new Recordset;

        $obMapeamento->recuperaTodos($rsRecordSet,'',' ORDER BY descricao');

        $this->setRotulo            ("Tipo de Objeto"                        );
        $this->setName              ("inCodTipoObjeto"                       );
        $this->setTitle             ("Selecione o tipo do objeto."           );
        $this->setNull              (true                                    );
        $this->addOption            ("","Selecione"                          );
        $this->setCampoID           ("cod_tipo_objeto"                       );
        $this->setCampoDesc         ("descricao"                             );
        $this->preencheCombo        ($rsRecordSet                            );
    }
}
?>
