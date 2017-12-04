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

class SeletorAno extends Componente
{
    public $obTxtAno;

    public function setHTML($valor)
    {
        $this->stHTML = $valor;
    }

    public function setAnoInicial($valor)
    {
        $this->inAnoInicial= $valor;
    }

    public function setBtnCima($valor)
    {
        $this->obBtnCima = $valor;
    }

    public function setBtnBaixo($valor)
    {
        $this->obBtnBaixo = $valor;
    }

    public function setTxtAno($valor)
    {
        $this->obTxtAno = $valor;
    }

    public function getAnoInicial()
    {
        return $this->inAnoInicial;
    }

    public function getHTML()
    {
        return $this->stHTML;
    }

    public function SeletorAno()
    {
        parent::Componente();
        $this->setDefinicao("seletorAno");

        include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao, "", "cod_periodo_movimentacao LIMIT 1");
        $inAnoInicioPerMov = substr($rsPeriodoMovimentacao->getCampo("dt_final"),-4);
        Sessao::write("inAnoInicioPerMov", $inAnoInicioPerMov);

        // Criando compo Text de ano
        $obTxtAno = new TextBox;
        $obTxtAno->setName                  ( "inAno"                                 );
        $obTxtAno->setId                    ( "inAno"                                 );
        $obTxtAno->setRotulo                ( "Ano"                                   );
        $obTxtAno->setTitle                 ( "Informe a competência."                );
        $obTxtAno->setSize                  ( 3                                       );
        $obTxtAno->setMaxLength             ( 4                                       );
        $obTxtAno->obEvento->setOnKeyUp     ("mascaraDinamico('2059', this, event);"  );
        $obTxtAno->obEvento->setOnBlur      ("checkSelectorDate(this, '".$inAnoInicioPerMov."');");
        $obTxtAno->setNull                  ( false                                   );

        // Criando botões cima e baixo
        $obBtnCima = new Button;
        $obBtnCima->setName  ("btMais_SelelotorAno");
        $obBtnCima->setId    ("btMais_SelelotorAno");
        $obBtnCima->setStyle ("height:12px; width:16px; background-image:url(".CAM_FW_IMAGENS."btnPraCima.gif); background-position: center; background-repeat:no-repeat;");
        $obBtnCima->setValue ("");

        $obBtnBaixo = new Button;
        $obBtnBaixo->setName  ("btMenos_SelelotorAno");
        $obBtnBaixo->setId    ("btMenos_SelelotorAno");
        $obBtnBaixo->setStyle ("height:12px; width:16px; background-image:url(".CAM_FW_IMAGENS."btnPraBaixo.gif); background-position: center; background-repeat:no-repeat;");
        $obBtnBaixo->setValue ("");

        $this->setTxtAno($obTxtAno);
        $this->setBtnCima($obBtnCima);
        $this->setBtnBaixo($obBtnBaixo);
    }

    public function montaHTML()
    {
        $stHTML = "";
        $inAnoInicioPerMov = Sessao::read("inAnoInicioPerMov");

        $this->obBtnCima->obEvento->setOnClick("increaseSelectorYear('".$this->obTxtAno->getId()."');".$this->obBtnCima->obEvento->getOnClick());
        $this->obBtnBaixo->obEvento->setOnClick("decreaseSelectorYear('".$this->obTxtAno->getId()."', '".$inAnoInicioPerMov."');".$this->obBtnBaixo->obEvento->getOnClick());

        $this->obTxtAno->setValue($this->getAnoInicial());

        if ($this->getDisabled()) {
            $obHdnAno = new Hidden();
            $obHdnAno->setName ($this->obTxtAno->getName());
            $obHdnAno->setId   ($this->obTxtAno->getId());
            $obHdnAno->setValue($this->getAnoInicial());
            $obHdnAno->montaHTML();
            $stHtmlAnoHdn = $obHdnAno->getHTML();

            $this->obTxtAno->setDisabled(true);
            $this->obTxtAno->setName($this->obTxtAno->getName()."Disabled");
            $this->obTxtAno->setId  ($this->obTxtAno->getId()."Disabled");
        }

        $this->obTxtAno->montaHTML();
        $this->obBtnCima->montaHTML();
        $this->obBtnBaixo->montaHTML();

        $stHtmlAno      = $this->obTxtAno->getHTML().$stHtmlAnoHdn;
        $stHtmlBtnCima  = $this->obBtnCima->getHTML();
        $stHtmlBtnBaixo = $this->obBtnBaixo->getHTML();

        if ($this->getDisabled()) {
            $stHtmlBtnCima  = "";
            $stHtmlBtnBaixo = "";
        }

        $stHTML .= "<table border='0' cellpadding='0' cellspacing='0'>   \n";
        $stHTML .= "    <tr>                                             \n";
        $stHTML .= "        <td rowspan='2' align='center' valign='top'> \n";
        $stHTML .= "             ".$stHtmlAno."                          \n";
        $stHTML .= "        </td>                                        \n";
        $stHTML .= "        <td>                                         \n";
        $stHTML .= "              ".$stHtmlBtnCima."                     \n";
        $stHTML .= "        </td>                                        \n";
        $stHTML .= "    <tr>                                             \n";
        $stHTML .= "        <td>                                         \n";
        $stHTML .= "               ".$stHtmlBtnBaixo."                   \n";
        $stHTML .= "        </td>                                        \n";
        $stHTML .= "    </tr>                                            \n";
        $stHTML .= "</table>                                             \n";

        $this->setHTML($stHTML);
    }

    public function show()
    {
        $this->montaHTML();
        $stHTML = $this->getHTML();
        echo $stHTML;
    }
}
?>
