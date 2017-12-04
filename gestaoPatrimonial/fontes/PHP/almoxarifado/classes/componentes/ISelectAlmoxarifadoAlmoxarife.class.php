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
    * Arquivo de popup de busca de CGM
    * Data de Criação: 27/02/2003

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Id: ISelectAlmoxarifadoAlmoxarife.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once ( CLA_SELECT );

class ISelectAlmoxarifadoAlmoxarife extends Select
{
    public $inCodAlmoxarifado;

    public function ISelectAlmoxarifadoAlmoxarife()
    {
        parent::Select();

        include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoAlmoxarife.class.php");
        $obRAlmoxarifadoAlmoxarife  = new RAlmoxarifadoAlmoxarife();
        $rsAlmoxarifado            = new Recordset;

        $obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCgm(Sessao::read('numCgm'));
        $obRAlmoxarifadoAlmoxarife->listarPermissao($rsAlmoxarifado,"",true);
        $obRAlmoxarifadoAlmoxarife->consultar();
        $this->setCodAlmoxarifado( $obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo() );

        $this->setRotulo            ("Almoxarifado"                          );
        $this->setName              ("inCodAlmoxarifado"                     );
        $this->setTitle             ("Selecione o almoxarifado."             );
        $this->setNull              (true                                    );
        $this->setCampoID           ("codigo"                                );
        $this->addOption            ("","Selecione"                          );
        $this->setCampoDesc         ("[codigo] - [nom_a]"                    );
        $this->preencheCombo        ($rsAlmoxarifado                         );
        //$this->setValue             ($inCodAlmoxarifadoPadrao                );
    }

    public function setCodAlmoxarifado($inCodAlmoxarifado)
    {
        $this->inCodAlmoxarifado = $inCodAlmoxarifado;
    }

    public function getCodAlmoxarifado()
    {
        return $this->inCodAlmoxarifado;
    }

    public function montaHTML()
    {
        $this->setValue             ($this->getCodAlmoxarifado()              );
        parent::montaHTML();
    }
}
?>
